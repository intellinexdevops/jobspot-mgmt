<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Google\Client;

class JobApplicationController extends Controller
{
    public function index()
    {
        return response()->json([
            'msg' => 'test'
        ]);
    }

    public function applyJob(Request $request)
    {
        $valicated = $request->validate([
            'user_id' => ['required', 'string', 'exists:users,id'],
            'post_id' => ['required', 'string', 'exists:posts,id'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'name' => ['required', 'string'],
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
        ]);

        $fcmToken = $this->getPosterFcmToken($valicated["user_id"]);

        if (!$fcmToken) {
            return response()->json(['error' => 'Job poster FCM token not found.'], 404);
        }

        $notificationSent = $this->sendNotification(
            $fcmToken,
            $valicated['title'],
            $valicated['body'],
        );

        if ($notificationSent) {
            return response()->json(['message' => 'Notification sent successfully.']);
        } else {
            return response()->json([
                'error' => 'Failed to send notification.',
                'token' => $fcmToken
            ], 500);
        }
    }

    private function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('/app/intellinex_5d5a2_686bda7af139.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        return $client->fetchAccessTokenWithAssertion()["access_token"];
    }


    private function getPosterFcmToken($posterId)
    {
        // Replace with your database query to fetch the FCM token
        $poster = DB::table('users')->where('id', $posterId)->first();

        return $poster->push_token ?? null;
    }

    private function sendNotification(
        String $fcmToken,
        String $title,
        String $body
    ) {
        // $serverKey = env('FCM_SERVER_KEY'); // Firebase server key from .env
        $accessToken = $this->getAccessToken();
        $url = 'https://fcm.googleapis.com/v1/projects/intellinex-5d5a2/messages:send';

        $data = [
            "message" => [
                "token" => $fcmToken,
                "notification"  => [
                    "title" => $title,
                    "body" => $body
                ],
                "data" => [
                    "title" => $title,
                    "body" => $body
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        return $response->successful();
    }
}
