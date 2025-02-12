<?php

namespace App\Helper;

class ResponseHelper
{

    public static function success($data, $msg, $status = "Success", $statusCode)
    {
        return response()->json([
            "code" => 1,
            "status" => $status,
            "msg" => $msg,
            "data" => $data
        ], $statusCode);
    }

    public static function error($msg, $status = "Error", $statusCode)
    {
        return response()->json([
            "code" => 0,
            "status" => $status,
            "msg" => $msg,
            "data" => null
        ], $statusCode);
    }
}
