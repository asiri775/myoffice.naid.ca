<?php


namespace Modules\User\Controllers;

use App\BaseModel;
use App\Helpers\CodeHelper;
use App\Helpers\Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Booking\Models\Payment;
use Modules\FrontendController;
use Modules\User\Events\RequestCreditPurchase;
use Modules\User\Models\Wallet\DepositPayment;
use Modules\User\Models\Wallet\Transaction;
use Modules\Vendor\Events\PayoutRequestEvent;
use Modules\Vendor\Models\VendorPayout;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends FrontendController
{
    public function wallet()
    {
        if (setting_item('wallet_module_disable')) {
            return redirect(route("user.profile.index"));
        }
        $row = auth()->user();
        
        $data = [
            'row' => $row,
            'page_title'         => __("Wallet"),
            'breadcrumbs'        => [
                [
                    'name'  => __('Wallet'),
                    'class' => 'active'
                ]
            ],
            'transactions' => $row->transactions()->with(['payment', 'author'])->orderBy('id', 'desc')->paginate(15)
        ];
        return view('User::frontend.wallet.index', $data);
    }
    public function buy()
    {
        if (setting_item('wallet_module_disable')) {
            return redirect(route("user.profile.index"));
        }
        $row = auth()->user();
        $booking = new \Modules\Booking\Controllers\BookingController();
        $data = [
            'row' => $row,
            'page_title'         => __("Buy credits"),
            'breadcrumbs'        => [
                [
                    'name'  => __('Wallet'),
                    'url' => route('user.wallet')
                ],
                [
                    'name'  => __('Buy credits'),
                    'class' => 'active'
                ],
            ],
            'wallet_deposit_lists' => setting_item_array('wallet_deposit_lists', []),
            'gateways' => $booking->getGateways()
        ];

        return view('User::frontend.wallet.buy', $data);
    }

    public function buyProcess(Request $request)
    {
        if (setting_item('wallet_module_disable')) {
            return redirect(route("user.profile.index"));
        }
        $row = auth()->user();
        $rules = [];
        $message = [];
        if (setting_item('wallet_deposit_type') == 'list') {
            $rules['deposit_option'] = 'required';
        } else {
            $rules['deposit_amount'] = 'required';
        }

        $payment_gateway = $request->input('payment_gateway');

        $gateways = get_payment_gateways();
        if (empty($payment_gateway)) {
            return redirect()->back()->with("error", __("Please select payment gateway"));
        }
        if (empty($payment_gateway) or empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway])) {
            return redirect()->back()->with("error", __("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$payment_gateway]($payment_gateway);

        if (!$gatewayObj->isAvailable()) {
            return redirect()->back()->with("error", __("Payment gateway is not available"));
        }
        if ($gRules = $gatewayObj->getValidationRules()) {
            $rules = array_merge($rules, $gRules);
        }
        if ($gMessages = $gatewayObj->getValidationMessages()) {
            $message = array_merge($message, $gMessages);
        }
        $rules['payment_gateway'] = 'required';
        $rules['term_conditions'] = 'required';

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            if (is_array($validator->errors()->messages())) {
                $msg = '';
                foreach ($validator->errors()->messages() as $oneMessage) {
                    $msg .= implode('<br/>', $oneMessage) . '<br/>';
                }
                return redirect()->back()->with('error', $msg);
            }
            return redirect()->back()->with('error', $validator->errors());
        }

        $deposit_option = [];

        if (setting_item('wallet_deposit_type') == 'list') {

            $wallet_deposit_lists = setting_item_array('wallet_deposit_lists', []);
            $deposit_option = $request->input('deposit_option');
            if (empty($wallet_deposit_lists[$deposit_option])) {
                return redirect()->back()->with("error", __("Deposit option is not valid"));
            }
            if (empty($wallet_deposit_lists[$deposit_option]['amount'])) {
                return redirect()->back()->with("error", __("Deposit option amount is not valid"));
            }
            if (empty($wallet_deposit_lists[$deposit_option]['credit'])) {
                return redirect()->back()->with("error", __("Deposit option credit is not valid"));
            }
            $deposit_amount = $wallet_deposit_lists[$deposit_option]['amount'];
            $deposit_credit = $wallet_deposit_lists[$deposit_option]['credit'];
            $deposit_option = $wallet_deposit_lists[$deposit_option];
        } else {

            $deposit_amount = $request->input('deposit_amount');
            $deposit_credit = $deposit_amount * setting_item('wallet_deposit_rate', 1);
            if ($deposit_amount < 0) {
                return redirect()->back()->with("error", __("Deposit option credit is not valid"));
            }
        }

        // dd($request->all());

        $payment = new DepositPayment();
        $payment->object_model = 'wallet_deposit';
        $payment->object_id = $row->id;
        $payment->status = 'draft';
        $payment->payment_gateway = $payment_gateway;
        $payment->amount = $deposit_amount;
        $payment->meta = json_encode([
            'credit' => $deposit_credit,
            'deposit_option' => $deposit_option
        ]);

        $payment->save();

        $res = $gatewayObj->processNormalCrediBuy($payment);
        // dd($res);

        $success = $res[0] ?? null;
        $message = $res[1] ?? null;
        $redirect_url = $res[2] ?? null;

        if ($success) {
            $transaction = $row->deposit($deposit_credit, [], false);
            $transaction->payment_id = $payment->id;
            $transaction->save();

            $payment->wallet_transaction_id = $transaction->id;
            $payment->code = $request->input('txnToken');
            $payment->save();
            if (empty($redirect_url) and $payment->status == 'completed') {
                // Send Email
                $payment->sendNewPurchaseEmail();
            }
            event(new RequestCreditPurchase($row, $payment));
        }

        if ($success and $payment->status == 'completed') $redirect_url = route('user.wallet');
        if ($redirect_url) {
            return redirect()->to($redirect_url)->with($success ? "success" : "error", $message);
        }
        return redirect()->back()->with($success ? "success" : "error", $message);
    }

    public function transactionHistory()
    {
        $user_id = Auth::id();

        $user = auth()->user();
        $query = $user->transactions()->with(['payment', 'author']);

        $searchFilters = request()->input('search_query');
        $searchFilters = CodeHelper::cleanArray($searchFilters);

        if (array_key_exists('from', $searchFilters) && $searchFilters['from']) {
            $from = CodeHelper::dateConvertion($searchFilters['from']);
            if (!isset($from)) {
                $from = Carbon::now()->startOfYear();
            } else {
                $from = $from . " 00:00:00";
            }
            $query->where('created_at', '>=', $from);
        }

        if (array_key_exists('to', $searchFilters) && $searchFilters['to']) {
            $to = CodeHelper::dateConvertion($searchFilters['to']);
            if (!isset($to)) {
                $to = Carbon::now()->endOfYear();
            } else {
                $to = $to . " 23:59:59";
            }
            $query->where('created_at', '<=', $to);
        }

        if (array_key_exists('status', $searchFilters) && $searchFilters['status']) {
            $status = trim($searchFilters['status']);
            if ($status != null) {
                switch ($status) {
                    case 'pending':
                        $query->where('confirmed', 0);
                        break;
                    case 'processing':
                        $query->where('confirmed', 0.5);
                        break;
                    case 'failed':  
                        $query->where('confirmed', -1);
                        break;
                    case 'confirmed':
                        $query->where('confirmed', 2);
                        break;
                }
            }
        }

        BaseModel::buildFilterQuery($query, [
            'type',
            'amount',
            'id'
        ]);

        return DataTables::eloquent($query)
            ->addColumn('checkboxes', function ($model) {
                $select = '<input type="checkbox" name="checkbox[]" value="' . $model->id . '">';
                return $select;
            })
            ->addColumn('idLink', function ($model) {
                return '<a href="' . route('user.transactionDetails', [$model->uuid]) . '">' . $model->id . '</a>';
            })
            ->addColumn('amount', function ($model) {
                return CodeHelper::formatPrice($model->amount);
            })
            ->addColumn('actions', function ($row) {
                $buttons = [
                    'view' => ['url' => route('user.transactionDetails', [$row->uuid]), 'class' => 'showTransactionDetails', 'extra' => ['data-id' => $row->uuid]],
                ];
                return BaseModel::getActionButtons($buttons);
            })
            ->addColumn('status', function ($model) {
                return '<span class="badge badge-lg badge-' . $model->status_class . '">' . strtoupper($model->status_name) ?? '' . '</span>';
            })
            ->addColumn('type', function ($model) {
                if ($model->type == "deposit") {
                    $model->type = "Deposits";
                }
                return ucfirst($model->type);
            })
            ->addColumn('date', function ($model) {
                return display_datetime($model->created_at);
            })
            ->rawColumns(['checkboxes', 'status', 'actions', 'idLink'])
            ->make(true);
    }

    public function transactionDetails(Request $request, $uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)->first();
        $data['transaction'] = $transaction;
        $data['page_title'] = "Transaction Details";
        if ($request->ajax()) {
            return view('User::frontend.wallet._transaction_details', $data);
        } else {
            return view('User::frontend.wallet.transaction_details', $data);
        }
    }

    public function withdraw()
    {
        if (setting_item('wallet_module_disable')) {
            return redirect(route("user.profile.index"));
        }
        $row = auth()->user();
        $data = [
            'row' => $row,
            'page_title'         => __("Wallet"),
            'breadcrumbs'        => [
                [
                    'name'  => __('Wallet'),
                    'class' => 'active'
                ]
            ],
            'transactions' => $row->transactions()->with(['payment', 'author'])->orderBy('id', 'desc')->paginate(15)
        ];
        return view('User::frontend.wallet.withdraw', $data);
    }

    public function prcoessWithdraw(Request $request)
    {
        $row = auth()->user();
        // dd($request->all());
        $amount = $request->input('amount');
        $depositTo = $request->input('deposit_to');

        $wallet = CodeHelper::getUserWallet($row);
        $balance = (float)$wallet->balance;

        if ($amount > 0 && trim($depositTo) != null) {

            $withdrawalFee = Constants::SERVICE_FEE;

            $totalAmount = ($amount + $withdrawalFee);

            if ($totalAmount <= $balance) {

                // die("ok");

                $payout = new VendorPayout();
                $payout->payout_method = 'manual';
                $payout->amount = $amount;
                $payout->note_to_admin = '';
                $payout->account_info = $depositTo;
                $payout->vendor_id = Auth::id();
                $payout->status = 'initial'; 

                $payout->fee = $withdrawalFee;
                $payout->total = $totalAmount;

                if ($payout->save()) {

                    $r = CodeHelper::addUserTransaction(
                        $row,
                        Constants::TRANSACTION_TYPE_WITHDRAWAL,
                        $payout->total,
                        Constants::DEBIT,
                        "WITHDRAWAL-" . $payout->id
                    );

                    CodeHelper::markTransactionConfirmed($r['transaction']);

                    event(new PayoutRequestEvent('insert', $payout));
                    
                    return redirect()->to('user/wallet')->with("success", "Withdrawal request has been sent");
                } else {
                    return redirect()->back()->with("error", "Failed to complete your request");
                }
            } else {
                return redirect()->back()->with("error", "Withdrawal request exceed wallet balance " . CodeHelper::formatPrice($balance));
            }
        } else {
            return redirect()->back()->with("error", "Amount and deposit details are required");
        }
    }
}
