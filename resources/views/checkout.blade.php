<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>

<div>
    <h1>Checkout</h1>
    <p>Total Amount: USD {{ $amount }}</p>
    <form action="{{config('payway.api_url')}}" method="post" target="aba_webservice" id="aba_merchant_request" >
        @csrf
        <input type="hidden" name="hash" value="{{$hash}}" id="hash">
        <input type="hidden" name="tran_id" value="{{$tran_id}}" id="tran_id">
        <input type="hidden" name="amount" value="{{$amount}}" id="amount">
        <input type="hidden" name="firstname" value="{{$firstname}}">
        <input type="hidden" name="lastname" value="{{$lastname}}">
        <input type="hidden" name="phone" value="{{$phone}}">
        <input type="hidden" name="email" value="{{$email}}">
        <input type="hidden" name="items" value="{{$items}}" id="items">
        <input type="hidden" name="return_params" value="{{$return_params}}">
        <input type="hidden" name="shipping" value="{{$shipping}}">
        <input type="hidden" name="currency" value="{{$currency}}" id="currency">
        <input type="hidden" name="type" value="{{$type}}">
        <input type="hidden" name="payment_option" value="{{$payment_option}}" id="payment_option">
        <input type="hidden" name="merchant_id" value="{{$merchant_id}}" id="merchant_id">
        <input type="hidden" name="req_time" value="{{$req_time}}" id="req_time">
    </form>
    <input type="button" id="checkout_button" value="Pay" />
</div>

<script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>

<script>

    $(document).ready(function() {
        $('#checkout_button').click(function() {
            AbaPayway.checkout();
        });
    });

</script>

    
</body>
</html>