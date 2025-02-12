<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Mail\SendNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendNotificationMailController extends Controller
{
    public function send(Request $request)
    {
        try {

            $validated = $request->validate([
                "to" => "required|email",
                "subject" => "required|string",
                "sender" => "required|string",
                "receiver" => "required|string",
                "position" => "required|string"
            ]);

            $receiver = $validated["receiver"];
            $position = $validated["position"];
            $subject = $validated["subject"];

            $response = Mail::to($validated["to"])->send(new SendNotificationMail($receiver, $position, $subject));

            return ResponseHelper::success($response, "Successffully sent email to " . $validated["to"], "success", 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), "error", 500);
        }
    }
}
