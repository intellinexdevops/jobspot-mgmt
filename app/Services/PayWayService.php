<?php

namespace App\Services;

class PayWayService
{
    /**
     * 
     * @return string
     */
    public function getApiUrl(): string
    {
        return config('payway.api_url');
    }

    /**
     * 
     * @return string
     */
    public function getHash($str)
    {
        $key = config('payway.api_key');
        return base64_encode(hash_hmac('sha512', $str, $key, true));
    }
}