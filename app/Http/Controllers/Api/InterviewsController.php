<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Storage;

class InterviewsController extends Controller
{
    public function index(Request $request)
    {

        $interviews = DB::table('interviews as i')
            ->join('users as c', 'i.candidate_id', '=', 'c.id')
            ->join('users as r', 'i.recruiter_id', '=', 'r.id')
            ->join('posts as p', 'i.post_id', '=', 'p.id')
            ->join('companies as co', 'p.company_id', '=', 'co.id')
            ->select(
                'i.*',
                'c.nickname as candidate',
                'c.avatar as candidate_profile',
                'r.nickname as recruiter',
                'p.title',
                'co.company_name'
            )
            ->get();

        if ($interviews->isEmpty()) {
            return ResponseHelper::error(
                "No interviews found",
                "Failed",
                404
            );
        }
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => "required|exists:users,id",
            'recruiter_id' => "required|exists:users,id",
            'post_id' => "required|exists:posts,id",
            'company_id' => "required|exists:companies,id",
            'application_id' => "required|exists:application,id",
            'interview_date' => "required|date",
            'interview_time' => "required",
            'location' => "required|string",
            'comments' => "nullable|string"
        ]);

        try {

            DB::beginTransaction();

            $candidate = DB::table('users')
                ->where('id', $validated['candidate_id'])
                ->first();

            $recruiter = DB::table('users')
                ->where('id', $validated['recruiter_id'])
                ->first();


            DB::table('interviews')->insert([
                'candidate_id' => $validated['candidate_id'],
                'recruiter_id' => $validated['recruiter_id'],
                'post_id' => $validated['post_id'],
                'company_id' => $validated['company_id'],
                'application_id' => $validated['application_id'],
                'interview_date' => $validated['interview_date'],
                'interview_time' => $validated['interview_time'],
                'location' => $validated['location'],
                'comments' => $validated['comments'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('application')
                ->where('id', $validated['application_id'])
                ->update(['status' => 'interviewed']);

            DB::table("notifications")->insert([
                "user_id" => $validated["candidate_id"],
                "sender_id" => $validated["recruiter_id"],
                "sender" => $recruiter->nickname,
                "title" => "Congratulation, {$candidate->nickname}! You are scheduled for an interview.",
                "body" => "{$recruiter->nickname} has scheduled you. Check your application now to prepare for your best opportunity. Good luck!!",
                "deeplink" => "-",
                "receiver" => $candidate->nickname,
                "status" => "unread",
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            DB::commit();

            $fcmToken = $this->getPosterFcmToken($validated["candidate_id"]);

            $this->sendNotification(
                $fcmToken,
                "Congratulation, {$candidate->nickname}! You are scheduled for an interview.",
                "{$recruiter->nickname} has scheduled you. Check your application now to prepare for your best opportunity. Good luck!!"
            );

            return ResponseHelper::success(
                [],
                "Successfully created interview",
                "success",
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(
                "Failed to create interview",
                $th->getMessage(),
                500
            );
        }
    }

    public function selectInterviews(Request $request)
    {
        $validated = $request->validate([
            "application_id" => "required|exists:interviews,application_id"
        ]);

        $interview = DB::table('interviews as i')
            ->leftJoin('posts as p', 'i.post_id', '=', 'p.id')
            ->leftJoin('users as u', 'i.candidate_id', '=', 'u.id')
            ->leftJoin('locations as l', 'p.location_id', '=', 'l.id')
            ->leftJoin('companies as c', 'i.company_id', '=', 'c.id')
            ->leftJoin('locations as cl', 'c.location_id', '=', 'cl.id')
            ->where('i.application_id', $validated['application_id'])
            ->select(
                'i.*',
                'p.title as job_title',
                'l.name as job_location',
                'c.company_name',
                'c.profile as company_profile',
                'u.nickname as candidate',
                'u.avatar as candidate_avatar',
                'u.mobile as candidate_mobile',
                'u.email as candidate_email',
                'cl.name as company_location'
            )
            ->first();

        $interview->company_profile = env('CURRENT_URL') . Storage::url($interview->company_profile);
        $interview->candidate_avatar = env('CURRENT_URL') . Storage::url($interview->candidate_avatar);

        return ResponseHelper::success(
            $interview,
            "Successfully",
            "success",
            200
        );
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
