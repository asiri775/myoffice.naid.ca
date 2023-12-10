<div class="form-section">
    <div class="gateways-table accordion" id="accordionExample">
        <input type="hidden" name="payment_gateway" value="two_checkout_gateway">
        @php
            $amount = str_replace("$", '', format_money($booking->total));
            $token = 'name205CAD2001' . date('Y-m-d') . number_format((float) $amount, 2, '.', '') . $booking->id;
            $hashedToken = hash('sha256', $token);
        @endphp

        <input type="hidden" id="merchantPgIdentifier" name="merchantPgIdentifier" value="205">
        <input type="hidden" id="secret_id" name="secret_id" value="2001">
        <input type="hidden" id="currency" name="currency" value="CAD">
        <input type="hidden" id="amount" name="amount" value="{{ $amount }}">
        <input type="hidden" id="orderId" name="orderId" value="{{ $booking->id }}">
        <input type="hidden" id="invoiceNumber" name="invoiceNumber" value="{{ $booking->id }}">
        <input type="hidden" id="successUrl" name="successUrl" value="http://myoffice.mybackpocket.co/booking/confirm/two_checkout_gateway">
        <input type="hidden" id="errorUrl" name="errorUrl" value="http://myoffice.mybackpocket.co/booking/cancel/two_checkout_gateway">
        <input type="hidden" id="storeName" name="storeName" value="name205">
        <input type="hidden" id="transactionType" name="transactionType" value="">
        <input type="hidden" id="timeout" name="timeout" value="">
        <input type="hidden" id="transactionDateTime" name="transactionDateTime" value="{{ date('Y-m-d') }}">
        <input type="hidden" id="language" name="language" value="EN">
        <input type="hidden" id="txnToken" name="txnToken" value="{{ $hashedToken }}">
        <input type="hidden" id="credits" name="credits" value="{{ $user !== null ? $user->balance : '' }}">
        <input type="hidden" id="itemList" name="itemList" value="list">
        <input type="hidden" id="otherInfo" name="otherInfo" value="">
        <input type="hidden" id="merchantCustomerPhone" name="merchantCustomerPhone" value="04353563535">
        <input type="hidden" id="merchantCustomerEmail" name="merchantCustomerEmail" value="customer@gmail.com">
    </div>
</div>
