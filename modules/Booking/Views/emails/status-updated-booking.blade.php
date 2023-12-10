@php
$get_country_name=get_country_name($booking->country);
$translation = $service->translateOrOrigin(app()->getLocale());
$lang_local = app()->getLocale();
$booking_number=$booking->id;
$booking_status=$booking->statusName;
if($booking->gatewayObj)
{
$payment_method=$booking->gatewayObj->getOption('name');
}
if($booking->gatewayObj and $note = $booking->gatewayObj->getOption('payment_note'))
{
$payment_note=clean($note);
}
$translation_title=clean($translation->title);
$service_getDetailUrl=$service->getDetailUrl();
if($translation->address)
{
$address=$translation->address;
}

if($booking->start_date && $booking->end_date)
{
    $display_start_date= display_date($booking->end_date);
    $display_end_date= display_date($booking->end_date);
    $booking_type_days='';
    if($booking->getMeta("booking_type") == "by_day")
    {
    $duration_days=$booking->duration_days;
    $booking_type_days='<tr>
            <td class="label">Days:</td>
            <td class="val">'.$duration_days.'</td>
        </tr>';
    }
    $duration_nights='';
    if($booking->getMeta("booking_type") == "by_night")
    {
    $duration_nights=$booking->duration_nights;
    $booking_type_nights='<tr>
            <td class="label">Nights:</td>
            <td class="val">'.$duration_nights.'</td>
        </tr>';
    }
}

$adults_text='';
if($meta = $booking->getMeta('adults'))
{
   $adults=$booking->getMeta('adults');
   $adults_text='<tr>
            <td class="label">Adults:</td>
            <td class="val"><strong>'.$adults.'</strong></td>
        </tr>';
}
$children_text='';
if($meta = $booking->getMeta('children'))
{
   $children=$booking->getMeta('children');
   $children_text='<tr>
            <td class="label">Children:</td>
            <td class="val"><strong>'.$children.'</strong></td>
        </tr>';
}
$price_item = $booking->total_before_extra_price;
$rental_price=format_money($price_item);
$extra_price = $booking->getJsonMeta('extra_price');
$extra='';
if(!empty($extra_price)){
 foreach($extra_price as $type)
       {
     $extra.='<tr>
        <td class="label">'.$type['name'].':</td>
        <td class="val no-r-padding"><strong>'.$type['total'].'</strong></td>
        </tr>';
        }
 }

$total= format_money($booking->total);
$paid= format_money($booking->paid);
if($booking->total > $booking->paid)
{
    $remain=format_money($booking->total - $booking->paid);
}
$booking_history_url=route("user.bookings.details");

$list_all_fee = [];
if(!empty($booking->buyer_fees)){
    $buyer_fees = json_decode($booking->buyer_fees , true);
    $list_all_fee = $buyer_fees;
}
if(!empty($vendor_service_fee = $booking->vendor_service_fee)){
    $list_all_fee = array_merge($list_all_fee , $vendor_service_fee);
}
$list_all_fee_text='';
if(!empty($list_all_fee)){
    foreach ($list_all_fee as $item)
    {
        $fee_price = $item['price'];
            if(!empty($item['unit']) and $item['unit'] == "percent"){
                $fee_price = ( $booking->total_before_fees / 100 ) * $item['price'];
            }
          $list_all_fee_text='<tr>
            <td class="label">'.$item['name'].'
                <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top" title="'.$item['desc'].'"></i>';
                if(!empty($item['per_person']) and $item['per_person'] == "on"){
                    $list_all_fee_text.=':'.$booking->total_guests * format_money( $fee_price );
                }
 $list_all_fee_text.='</td><td class="val">';
               if(!empty($item['per_person']) and $item['per_person'] == "on")
                 {
 $list_all_fee_text.=format_money( $fee_price * $booking->total_guests);
                 }else
                {
$list_all_fee_text.=format_money( $fee_price );
                }
$list_all_fee_text.='</td></tr>';

    }
}

$customer_info= <<<HEREDOC
<div style="text-align: center;
                margin-bottom: 30px;
                font-size: 24px;
                font-weight: 500;">Space information</div>
<div style="overflow-x:auto;">
    <table style="width: 100%;" cellspacing="0" cellpadding="0">
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Booking Number</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">#$booking_number</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Booking Status</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">$booking_status</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Payment method</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">$payment_method</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Space name</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;"><a href="$service_getDetailUrl">$translation_title</a></td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Address</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">$address</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Start date</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">$display_start_date</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">End date:</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">$display_end_date</td>
        </tr>
        $booking_type_days
        $duration_nights
        $adults_text
        $children_text
        <tr>
            <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;">Pricing</td>
            <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;">
                <table style="text-align: left;
                margin: 0px;
                padding: 0px;
                list-style: none;" width="100%">
                    <tr>
                        <td style="padding: 10px; font-weight: 500;border-bottom: 1px solid #EAEEF3;"> Rental price</td>
                        <td style="padding: 10px;text-align: right;font-weight: 500;border-bottom: 1px solid #EAEEF3;"><strong>$rental_price</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="label-title"><strong>Extra Prices:</strong></td>
                    </tr>
                    <tr class="">
                        <td colspan="2" class="no-r-padding no-b-border">
                            <table width="100%">
                                $extra
                            </table>
                        </td>
                    </tr>
                    $list_all_fee_text
                </table>
            </td>
        </tr>
        <tr>
            <td style="font-weight: 500;border-bottom: 1px solid #EAEEF3; padding: 10px;font-size: 21px;">Total</td>
            <td style="text-align: right;border-bottom: 1px solid #EAEEF3;padding: 10px;font-size: 21px;"><strong style="color: #FA5636">$total</strong></td>
        </tr>
        <tr>
            <td style="font-weight: 500;border-bottom: 1px solid #EAEEF3; padding: 10px;font-size: 21px;">Paid</td>
            <td style="text-align: right;border-bottom: 1px solid #EAEEF3;padding: 10px;font-size: 21px;"><strong style="color: #FA5636">$paid</strong></td>
        </tr>
        <tr>
            <td style="font-weight: 500;border-bottom: 1px solid #EAEEF3; padding: 10px;font-size: 21px;">Remain</td>
            <td style="text-align: right;border-bottom: 1px solid #EAEEF3;padding: 10px;font-size: 21px;"><strong style="color: #FA5636">$remain</strong></td>
        </tr>
    </table>
</div>
<div style="margin-top: 20px;text-align: center;">
    <a href="$booking_history_url" target="_blank" style="background: #5191FA;
color: white;display: inline-block;text-align: center;
vertical-align: middle;line-height: 1.5;
border: none;box-shadow: none;
border-radius: 3px;
padding: 7px 20px;transition: background .2s, color .2s;
font-size: 14px;
font-weight: 500;
text-decoration: none;">Manage Bookings</a>
</div>
</div>
HEREDOC;

$variableArray = array(
    'first_name'=>ucfirst($name),
    'space_information'=>$customer_info,
);

$templateHTML = $template['content'];

foreach ($variableArray as $key => $value) {
    $templateHTML = str_replace("{".$key."}", $value, $templateHTML);
}

@endphp

{!! $templateHTML !!}


