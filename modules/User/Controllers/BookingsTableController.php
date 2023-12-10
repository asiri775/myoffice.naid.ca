<?php

namespace Modules\User\Controllers;

use App\BaseModel;
use App\Helpers\CodeHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Payment;
use Modules\Core\Models\Terms;
use Modules\Location\Models\Location;
use Modules\Space\Models\Space;
use Yajra\DataTables\Facades\DataTables;
use Modules\Space\Models\SpaceTerm;

class BookingsTableController extends Controller
{
    public function dateConvertion($date)
    {
        $d = explode('/', $date);
        $date = $d[2] . '-' . $d[0] . '-' . $d[1];
        return $date;
    }

    //booking history datatable
    public function __invoke(Request $request)
    {
        $user_id = Auth::user()->id;

        $bookings = Booking::orderBy('id', 'DESC')->where(function ($query) use ($user_id) {
            $query->where('customer_id', '=', $user_id)
                ->orWhere('vendor_id', '=', $user_id);
        });

        $showOnlyArchived = false;

        $searchQuery = request()->search_query;

        if (array_key_exists('date_option', $searchQuery)) {
            switch ($searchQuery['date_option']) {
                case 'yesterday':
                    $bookings = $bookings->whereDate('start_date', '=', Carbon::now()->subDay());
                    break;
                case 'today':
                    $bookings = $bookings->whereDate('start_date', '=', Carbon::now());
                    break;
                case 'this_weekdays':
                    $start = Carbon::now()->startOfWeek();
                    $end = Carbon::now()->endOfWeek()->subDays(2);
                    $bookings = $bookings->whereBetween('start_date', [$start, $end]);
                    break;
                case 'this_whole_week':
                    $start = Carbon::now()->startOfWeek();
                    $end = Carbon::now()->endOfWeek();
                    $bookings = $bookings->whereBetween('start_date', [$start, $end]);
                    break;
                case 'this_month':
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
                    $bookings = $bookings->whereBetween('start_date', [$start, $end]);
                    break;
                case 'this_year':
                    $start = Carbon::now()->startOfYear();
                    $end = Carbon::now()->endOfYear();
                    $bookings = $bookings->whereBetween('start_date', [$start, $end]);
                    break;
                default:
                    break;
            }
        }

        if (array_key_exists('status', $searchQuery)) {
            switch ($searchQuery['status']) {
                case 'archived':
                    $showOnlyArchived = true;
                    break;
                case 'scheduled':
                    // $bookings = $bookings->whereDate('end_date', '>=', Carbon::now());
                    $bookings = $bookings->where('status', 'scheduled');
                    break;
                case 'pending':
                    $bookings = $bookings->where('status', 'draft');
                    break;
                case 'history':
                    // $bookings = $bookings->whereDate('end_date', '<', Carbon::now());
                    $bookings = $bookings->where('status', 'complete');
                    break;
                case 'all':
                    $bookings = $bookings;
                    break;
                default:
                    break;
            }
        }

        if (array_key_exists('transaction_status', $searchQuery)) {
            switch ($searchQuery['transaction_status']) {
                case 'paid':
                    $bookings = $bookings->leftJoin(Payment::getTableName(), 'payment_id', '=', Payment::getTableName() . '.id')
                        ->where(function ($query) {
                            $query->where(Payment::getTableName() . '.status', '=', 'completed');
                        })->select(Payment::getTableName() . '.id');
                    break;
                case 'unpaid':
                    $bookings = $bookings->leftJoin(Payment::getTableName(), Booking::getTableName() . '.payment_id', '=', Payment::getTableName() . '.id')
                        ->where(function ($query) {
                            $query->where(Payment::getTableName() . '.status', '=', 'draft')->orWhereNull(Booking::getTableName() . '.payment_id');
                        })->select(Payment::getTableName() . '.id');
                    break;
                case 'fail':
                    $bookings = $bookings->leftJoin(Payment::getTableName(), 'payment_id', '=', Payment::getTableName() . '.id')
                        ->where(function ($query) {
                            $query->where(Payment::getTableName() . '.status', '=', 'fail');
                        })->select(Payment::getTableName() . '.id');
                    break;
                default:
                    break;
            }
        }

        if (array_key_exists('from', $searchQuery) && $searchQuery['from']) {
            $from = $this->dateConvertion($searchQuery['from']);
            if (!isset($from)) {
                $from = Carbon::now()->startOfYear();
            } else {
                $from = $from . " 00:00:00";
            }
            $bookings = $bookings->where('start_date', '>=', $from);
        }

        if (array_key_exists('to', $searchQuery) && $searchQuery['to']) {
            $to = $this->dateConvertion($searchQuery['to']);
            if (!isset($to)) {
                $to = Carbon::now()->startOfYear();
            } else {
                $to = $to . " 23:59:59";
            }
            $bookings = $bookings->where('end_date', '<=', $to);
        }

        // if (array_key_exists('city', $searchQuery) && $searchQuery->city != '') {
        //     $city_id = $searchQuery->city;
        //     $ids = Space::where('location_id', $city_id)->pluck('id')->toArray();
        //     $bookings = $bookings->whereIn('object_id', $ids);
        // }

        if (array_key_exists('search', $searchQuery) && $searchQuery['search'] != '') {
            $searchQueryData = trim($searchQuery['search']);
            if ($searchQueryData != null) {
                $bookings = $bookings->leftJoin(Space::getTableName(), Space::getTableName() . '.id', '=', Booking::getTableName() . '.object_id')
                    ->where(function ($query) use ($searchQueryData) {
                        $query->where(Space::getTableName() . '.address', 'like', '%' . $searchQueryData . '%');
                        $query->orWhere(Booking::getTableName() . '.id', 'like', '%' . $searchQueryData . '%');
                    })->select(Space::getTableName() . '.id');
            }
        }

        if (array_key_exists('space', $searchQuery) && $searchQuery['space'] != '') {
            $spaceSearchData = trim($searchQuery['space']);
            if ($spaceSearchData != null) {
                $bookings = $bookings->leftJoin(Space::getTableName(), Space::getTableName() . '.id', '=', Booking::getTableName() . '.object_id')
                    ->where(function ($query) use ($spaceSearchData) {
                        $query->where(Space::getTableName() . '.title', 'like', '%' . $spaceSearchData . '%');
                        $query->orWhere(Space::getTableName() . '.id', 'like', '%' . $spaceSearchData . '%');
                    })->select(Space::getTableName() . '.id');
            }
        }

        if (array_key_exists('booking_status', $searchQuery) && $searchQuery['booking_status'] != '') {
            $booking_status = $searchQuery['booking_status'];
            if ($booking_status == "archived") {
                $showOnlyArchived = true;
            } else {
                $bookings = $bookings->where('status', $booking_status);
            }
        }

        if (array_key_exists('category', $searchQuery) && $searchQuery['category'] != '') {
            $category_id = $searchQuery['category'];
            $space_ids = SpaceTerm::where('term_id', $category_id)->pluck('target_id')->toArray();
            $bookings = $bookings->whereIn('object_id', $space_ids);
        }

        if (array_key_exists('id', $searchQuery) && $searchQuery['id'] != '') {
            $id = $searchQuery['id'];
            $bookings = $bookings->where('id', $id);
        }

        if (array_key_exists('amount', $searchQuery) && $searchQuery['amount'] != '') {
            $amount = $searchQuery['amount'];
            $bookings = $bookings->where('total', 'LIKE', '%' . $amount . '%');
        }

        if ($showOnlyArchived) {
            $bookings = $bookings->where('is_archive', 1);
        } else {
            $bookings = $bookings->where('is_archive', '!=', 1);
        }

        $tableColumns = CodeHelper::getTableColumns(Booking::getTableName());
        $bookings = $bookings->select($tableColumns);

        // print_r($bookings->getBindings());
        // echo $bookings->toSql();die; 

        $bookings = $bookings->get();

        $dataTable = DataTables::of($bookings)
            ->addColumn('checkboxes', function ($booking) {
                $select = '<input type="checkbox" name="checkbox[]" value="' . $booking->id . '">';
                return $select;
            })
            ->addColumn('title', function ($booking) {
                $space = Space::where('id', $booking->object_id)->first();
                if ($space == null) {
                    return '-';
                }
                $title = $space->translateOrOrigin(app()->getLocale());
                return '<a target="_blank" href="' . $space->getDetailUrl($include_param ?? true) . '">' .
                    clean($title->title) . '
                                </a>';
            })
            ->addColumn('address', function ($booking) {
                $space = Space::where('id', $booking->object_id)->first();
                if ($space == null) {
                    return '-';
                }
                return $space->address;
            })
            ->addColumn('categories', function ($booking) {
                $categories = SpaceTerm::where('target_id', $booking->object_id)->pluck('term_id')->toArray();
                if (!empty(Terms::whereIn('id', $categories)->where('attr_id', 3)->pluck('name')->toArray())) {
                    return Terms::whereIn('id', $categories)->where('attr_id', 3)->pluck('name')->toArray();
                } else {
                    return 'Not Available';
                }
            })
            ->addColumn('id', function ($booking) {
                return '<a target="_blank" href="' . route('user.single.booking.detail', $booking->id) . '">' . $booking->id . '</a>';
            })
            ->addColumn('start_date', function ($booking) {
                $start_date = display_date($booking->start_date);
                return '<a target="_blank" href="' . route('user.single.booking.detail', $booking->id) . '">' . $start_date . '</a>';
            })
            ->addColumn('end_date', function ($booking) {
                $end_date = display_date($booking->end_date);
                return '<a target="_blank" href="' . route('user.single.booking.detail', $booking->id) . '">' . $end_date . '</a>';
            })
            ->addColumn('totalFormatted', function ($booking) {
                $total = CodeHelper::formatPrice($booking->total);
                return $total;
            })
            ->addColumn('guest', function ($booking) {
                $customerId = $booking->customer_id;
                $customer = User::where('id', $customerId)->first();
                if ($customer != null) {
                    return '<a href="' . route('user.profile.publicProfile', $booking->customer_id) . '">' . $customer->first_name . " " . $customer->last_name . " (Guest#" . $customer->id . ")" . '<a/>';
                }
                return '-';
            })
            ->addColumn('booking_status', function ($booking) {
                $book_status = $booking->statusText();
                $book_class = $booking->statusClass();
                return '<span class="status-btn ' . $book_class . '">' . strtoupper($book_status) . '</span>';
            })
            ->addColumn('transaction_status', function ($booking) {
                $payment = Payment::where('booking_id', $booking->id)->first();
                if ($payment) {
                    $payment_status = $payment->statusText();
                } else {
                    $payment_status = "UNPAID";
                }
                return strtoupper($payment_status);
            })
            ->addColumn('actions', function ($booking) {
                $buttons = [
					'edit' => ['url' => 'javascript:;', 'class' => 'modifySingleBooking', 'extra' => ['data-value' => $booking->id, 'data-details' => json_encode($booking)]],                  
					'view' => ['url' => route('user.single.booking.detail', $booking->id)],
                    'invoice' => ['url' => route('user.booking.invoice', $booking->code)],
                    'share' => ['url' => route('user.single.booking.detail', $booking->id), 'class' => 'sharer'],
                    'archive' => ['url' => route('user.booking.archive', $booking->id)],
					'checkin' => ['url' => route('user.booking.checkin', $booking->id),  'class' => 'checkInMessage', 'style'=>'background-image:url({{asset(icon/mo_checkin.svg)}})'],
					'checkout' => ['url' => route('user.booking.checkout', $booking->id), 'class' => 'checkOutMessage','style'=>'background-image:url({{asset(icon/mo_checkout.svg)}})'],
			    ];
                return BaseModel::getActionButtons($buttons);
            })
            ->rawColumns(['title', 'city', 'booking_status', 'guest', 'transaction_status', 'checkboxes', 'actions', 'id', 'start_date', 'end_date', 'booking_status'])
            ->make(true);

        $quantity = count($bookings);
        $grandTotal = $bookings->sum('total');

        $pageTotal =  0;

        $data = $dataTable->getData()->data;
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $item) {
                $pageTotal = $pageTotal + $item->total;
            }
        }

        $dataTable = CodeHelper::passDataToDatatableResponse($dataTable, [
            'quantity' => CodeHelper::formatNumber($quantity),
            'pageTotal' => CodeHelper::formatPrice($pageTotal),
            'grandTotal' => CodeHelper::formatPrice($grandTotal),
        ]);


        return $dataTable;
    }
}
