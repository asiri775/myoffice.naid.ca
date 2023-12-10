@extends('Layout::empty')
@section('head')
    <style type="text/css">
        html,
        body {
            background: #f0f0f0;
        }

        .bravo_topbar,
        .bravo_header,
        .bravo_footer {
            display: none;
        }

        .invoice-amount {
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 20px;
            display: inline-block;
            text-align: center;
        }

        .email_new_booking .b-table {
            width: 100%;
        }

        .email_new_booking .val {
            text-align: right;
        }

        .email_new_booking td,
        .email_new_booking th {
            padding: 5px;
        }

        .email_new_booking .val table {
            text-align: left;
        }

        .email_new_booking .b-panel-title,
        .email_new_booking .booking-number,
        .email_new_booking .booking-status,
        .email_new_booking .manage-booking-btn {
            display: none;
        }

        .email_new_booking .fsz21 {
            font-size: 21px;
        }

        .table-service-head {
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .table-service-head th {
            padding: 5px 15px;
        }

        #invoice-print-zone {
            background: white;
            padding: 15px;
            margin: 90px auto 40px auto;
            max-width: 1025px;
        }

        .invoice-company-info {
            margin-top: 15px;
        }

        .invoice-company-info p {
            margin-bottom: 2px;
            font-weight: normal;
        }

        .invoice-brand-details {
            text-align: center;
        }

        table {
            table-layout: fixed
        }

        .twoinline th.leftdata,
        .twoinline th.rightdata {
            width: 50%;
            vertical-align: top;
        }

        th.leftdata,
        th.rightdata {
            padding: 0 15px;
        }

        .twoinline th.rightdata {
            text-align: right;
        }

        table.inv-table {
            width: 100%;
            text-align: left;
            margin: 15px 0;
        }

        table.inv-table.center {
            text-align: center;
        }

        table.inv-table th {
            background: #000;
            color: #FFC106;
            text-transform: capitalize;
        }

        table.inv-table th,
        table.inv-table td {
            padding: 7px;
        }

        h2.invoice-main-title {
            font-size: 45px;
            color: #000;
            font-weight: 600;
            margin: 15px 0;
        }

        .twoinline table.inv-table {
            width: 100%;
        }

        .twoinline .rightdata table.inv-table {
            margin-right: 0;
            margin-left: auto
        }

        .brand-logo img {
            height: 100px;
            width: auto;
            max-width: auto !important;
            margin-top: 50px;
        }

        .invoice-table {
            table-layout: fixed;
        }

        .invoice-table td {
            border-bottom: 1px solid #ccc;
            height: 35px;
        }

        .invoice-table tr td:first-child,
        .invoice-table tr th:first-child {
            width: 65%;
        }

        .inv-table td {
            font-weight: normal;
        }

        .invoice-table tr td:not(:first-child) {
            border-left: 1px solid #ccc;
        }

        .invoice-make-it-count {
            margin-top: 35px;
            font-size: 12px;
            font-weight: normal;
        }

        .invoice-make-it-count h6 {
            font-weight: 600;
            font-size: 14px;
        }

        .invoiceextrainfo {
            padding-top: 0px !important;
            text-align: center;
            width: 100%;
        }

        .invoiceextrainfo p {   
            text-align: center;
            margin: 2px 0;
            font-size: 12px;
            font-weight: normal;
        }

        .inv-sub-table {
            max-width: 340px;
        }

        .inv-sub-table td {
            border-bottom: 1px solid #000;
        }

        .inv-sub-table td:first-child {
            text-align: right;
        }

        .inv-sub-table td:nth-child(2) {
            width: 35px;
            max-width: 35px;
        }

        .invoice-table td:nth-child(2), .invoice-table th:nth-child(2){
            text-align: center;
        }

        .inv-sub-table td:nth-child(3) {
            text-align: right;
        }

        .inv-total-table {
            border: 2px solid #000;
        }

        .noborder {
            border: none !important;
        }

        td.no-r-padding.no-b-border {
            padding: 0;
            border: none;
        }
    </style>
    <?php
    $translation = $service->translateOrOrigin(app()->getLocale());
    $lang_local = app()->getLocale();
    ?>
    <link href="{{ asset('module/user/css/user.css') }}" rel="stylesheet">
    <script>
   //window.print();
   
	</script>


    <div id="invoice-print-zone">
        <table width="100%" cellspacing="0" cellpadding="0">
            <thead>
                <tr class="twoinline">
                    <th width="50%" class="leftdata">
                        <div class="invoice-brand-details">
                            <div class="brand-logo">
                                <img src="{{ asset('user_assets/img/logo-black-main.png') }}"
                                    alt="{{ setting_item('site_title') }}">
                            </div>
                            <div class="invoice-company-info">
                                {!! setting_item_with_lang('invoice_company_info') !!}
                            </div>
                        </div>
                    </th>
                    <th width="50%" class="rightdata">
                        <h2 class="invoice-main-title">{{ __('INVOICE') }}</h2>
                        <table class="inv-table center">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                            </tr>
                            <tr>
                                <td>{{ __('Invoice #: :number', ['number' => $booking->id]) }}</td>
                                <td>{{ __(':date', ['date' => display_date($booking->created_at)]) }}</td>
                            </tr>
                        </table>
                        <table class="inv-table center">
                            <tr>
                                <th>Customer Id</th>
                                <th>Booking Ref#</th>
                            </tr>
                            <tr>
                                <td>{{ $booking->customer_id }}</td>
                                <td>{{ $booking->id }}</td>
                            </tr>
                        </table>
                    </th>
                </tr>
                <tr class="twoinline">
                    <th width="50%" class="leftdata">
                        <div class="invoice-bill-options">
                            <table class="inv-table">
                                <tr>
                                    <th>Bill To</th>
                                </tr>
                                <tr>
                                    <td>
                                        <span>{{ $booking->first_name . ' ' . $booking->last_name }}</span> <br>
                                        <span>{{ $booking->email }}</span><br>
                                        <span>{{ $booking->phone }}</span><br>
                                        <span>{{ $booking->address }}</span><br>
                                        <span>{{ implode(', ', [$booking->city, $booking->state, $booking->zip_code, get_country_name($booking->country)]) }}</span><br>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </th>
                    <th width="50%" class="rightdata">
                        <table class="inv-table">
                            <tr>
                                <th>Booking Details</th>
                            </tr>
                            <tr>
                                <td>
                                    {!! clean($translation->title) !!} <br>
                                    {{ $translation->address }} <br>
                                    Host: <a
                                        href="{{ route('user.profile.publicProfile', $booking->vendor_id) }}">{{ $booking->vendor->getPublicName() }}</a>
                                    <br>
                                    E-Mail: {{ $booking->vendor->email }}<br>
                                    <br>
                                    Arrival: {{ display_date($booking->start_date) }}<br>
                                    Departure: {{ display_date($booking->end_date) }}
                                </td>
                            </tr>
                        </table>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" width="100%" class="leftdata">
                        <table class="inv-table invoice-table">
                            <tr>
                                <th>Description</th>
                                <th>$</th>
                                <th>Amount</th>
                            </tr>
                            <tr>
                                <td class="label">
                                    {{ __('Rental price') }}
                                </td>
                                <td>$</td>
                                <td class="val">
                                    @php
                                        $price_item = $booking->total_before_extra_price;
                                    @endphp
                                    <span> {{ format_money($price_item) }}</span>
                                </td>
                            </tr>
                            @php $extra_price = $booking->getJsonMeta('extra_price')@endphp

                            @if (!empty($extra_price))
                                <tr class="">
                                    <td colspan="3" class="no-r-padding no-b-border">
                                        <table width="100%">
                                            @foreach ($extra_price as $type)
                                                <tr>
                                                    <td class="label">{{ $type['name'] }}:</td>
                                                    <td>$</td>
                                                    <td class="val no-r-padding">
                                                        <span>{{ format_money($type['total'] ?? 0) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endif

                            @php
                                $list_all_fee = [];
                                if (!empty($booking->buyer_fees)) {
                                    $buyer_fees = json_decode($booking->buyer_fees, true);
                                    $list_all_fee = $buyer_fees;
                                }
                                if (!empty(($vendor_service_fee = $booking->vendor_service_fee))) {
                                    $list_all_fee = array_merge($list_all_fee, $vendor_service_fee);
                                }
                            @endphp
                            @if (!empty($list_all_fee))
                                @foreach ($list_all_fee as $item)
                                    @php
                                        $fee_price = $item['price'];
                                        if (!empty($item['unit']) and $item['unit'] == 'percent') {
                                            $fee_price = ($booking->total_before_fees / 100) * $item['price'];
                                        }
                                    @endphp
                                    <tr>
                                        <td class="label">
                                            {{ $item['name_' . $lang_local] ?? $item['name'] }}
                                            <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top"
                                                title="{{ $item['desc_' . $lang_local] ?? $item['desc'] }}"></i>
                                            @if (!empty($item['per_person']) and $item['per_person'] == 'on')
                                                : {{ $booking->total_guests }} *
                                                {{ format_money($fee_price) }}
                                            @endif
                                        </td>
                                        <td>$</td>
                                        <td class="val">
                                            @if (!empty($item['per_person']) and $item['per_person'] == 'on')
                                                {{ format_money($fee_price * $booking->total_guests) }}
                                            @else
                                                {{ format_money($fee_price) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </th>
                </tr>
                <tr class="twoinline">
                    <th width="50%" class="leftdata">
                        <div class="invoice-make-it-count">
                            <div class="img">

                            </div>
                            <div class="content">
                                <h6>Make It Count </h6>
                                <p>Help Us Make A Difference! Your small micro donation will go
                                    towards providing free services and programs for Mental
                                    Health. In addition, this Merchant will also generously match
                                    your donation.</p>
                                <p>Click Here to learn more about this program and the Janeen
                                    Foundation</p>
                            </div>
                        </div>
                    </th>
                    <th width="50%" class="rightdata">
                        {{-- <table class="inv-table inv-sub-table">
                            <tr>
                                <td class="noborder" class="val" colspan="3">
                                    <table class="pricing-list" width="100%">
                                        

                                    </table>
                                </td>
                            </tr>

                        </table> --}}
                        <table class="inv-table inv-sub-table inv-total-table">
                            <tr>
                                <td class="label fsz21">{{ __('Total') }}</td>
                                <td>$</td>
                                <td class="val fsz21"><strong
                                        style="color: #FA5636">{{ format_money($booking->total) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="label fsz21">{{ __('Paid') }}</td>
                                <td>$</td>
                                <td class="val fsz21"><strong
                                        style="color: #FA5636">{{ format_money($booking->paid) }}</strong></td>
                            </tr>
                            @if ($booking->total > $booking->paid)
                                <tr>
                                    <td class="label fsz21">{{ __('Remain') }}</td>
                                    <td>$</td>
                                    <td class="val fsz21"><strong
                                            style="color: #FA5636">{{ format_money($booking->total - $booking->paid) }}</strong>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" width="100%" class="leftdata invoiceextrainfo">
                        <p>If you have any questions about this invoice, please contact</p>
                        <p><b>MyOffice Accounts: accounting@myoffice.ca</b></p>
                        <p>HST#000000000 RT0001</p>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@section('footer')
   <script type="text/javascript" src="{{ asset('module/user/js/user.js') }}"></script>
@endsection
