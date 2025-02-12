<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayWayService;

class PayWayController extends Controller
{
    protected $payWayService;

    public function __construct(PayWayService $payWayService)
    {
        $this->payWayService = $payWayService;
    }

    public function index()
    {
        $item = [
            ['name' => 'React Native', 'quantity' => '1', 'price' => '99'],
            ['name' => 'Spring Boot', 'quantity' => '1', 'price' => '99'],
            ['name' => 'Laravel', 'quantity' => '1', 'price' => '99'],
        ];

        $items = base64_encode(json_encode($item));

        $req_time = time();

        $tran_id = 'INT-' . time();

        $amount = '10.50';

        $firstname = 'John';
        $lastname = 'Doe';
        $email = 'john.doe@example.com';
        $phone = '0123456789';
        $return_params = "You have been successfully purchased";
        $type = 'purchase';
        $currency = 'USD';
        $shipping = '0.00';

        $merchant_id = config('payway.merchant_id');
        $payment_option = 'cards';


        $hash = $this->payWayService->getHash(
            $req_time . $merchant_id . $tran_id . $amount . $items . $shipping . $firstname . $lastname
                . $email . $phone . $type . $payment_option . $currency . $return_params
        );

        return view('checkout', compact(
            'hash',
            'tran_id',
            'amount',
            'firstname',
            'lastname',
            'phone',
            'email',
            'items',
            'return_params',
            'shipping',
            'currency',
            'type',
            'payment_option',
            'merchant_id',
            'req_time',
        ));
    }
}
