<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{

    public function index(Request $request)
    {
        $company_id = $request->company_id;

        $applications = DB::table("application")
            ->leftJoin("users", "user_id", "=", "users.id")
            ->join('posts', 'post_id', '=', 'posts.id')
            ->where("application.company_id", $company_id)
            ->select(
                'application.*',
                'users.username',
                'users.avatar',
                'users.nickname',
                'posts.title'
            )
            ->paginate(10);

        return ResponseHelper::success(
            $applications,
            "",
            "" . $company_id . "",
            200
        );
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            "user_id" => "required|exists:users,id",
            "post_id" => "required|exists:posts,id",
            "company_id" => "required|exists:companies,id",
            "recruiter_id" => "required|exists:users,id",
            "resume_id" => "required|exists:resume,id",
            "sender" => "string",
            "title" => "required|string",
            "body" => "required|string",
            "deeplink" => "required|string",
            "receiver" => "required|string",
        ]);

        try {
            DB::beginTransaction();

            DB::table("application")->insert([
                "user_id" => $validated["user_id"],
                "post_id" => $validated["post_id"],
                "recruiter_id" => $validated["recruiter_id"],
                "company_id" => $validated["company_id"],
                "resume_id" => $validated["resume_id"],
                "status" => "applied",
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            DB::table("notifications")->insert([
                "user_id" => $validated["recruiter_id"],
                "sender_id" => $validated["user_id"],
                "sender" => $validated["sender"],
                "title" => $validated["title"],
                "body" => $validated["body"],
                "deeplink" => $validated["deeplink"],
                "receiver" => $validated["receiver"],
                "status" => "unread",
                "created_at" => now(),
                "updated_at" => now(),
            ]);

            $fcmToken = $this->getPosterFcmToken($validated["recruiter_id"]);

            $this->sendNotification(
                $fcmToken,
                $validated['title'],
                $validated['body'],
            );

            DB::commit();

            return ResponseHelper::success(
                null,
                "Successfully applied.",
                "success",
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function selectApplication(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $applications = DB::table('application as a')
            ->leftJoin('posts as p', 'a.post_id', '=', 'p.id')
            ->leftJoin('companies as c', 'a.company_id', '=', 'c.id')
            ->leftJoin('locations as l', 'c.location_id', '=', 'l.id')
            ->where('a.user_id', $validated['user_id'])
            ->select(
                'a.*',
                'p.title',
                'p.description',
                'c.company_name',
                'c.profile',
                'l.name as address'
            )
            ->get();

        $applications->each(function ($app) {
            $app->profile = env('CURRENT_URL') . Storage::url($app->profile);
        });

        if ($applications->count() > 0) {
            return ResponseHelper::success(
                $applications,
                "Successfully fetched applications",
                "success",
                200
            );
        }
        return ResponseHelper::error(
            "Failed to fetch application",
            "error",
            404
        );
    }
    public function selectApplicationStatus(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:applied,shortlisted,interviewed,accepted,rejected'
        ]);

        $applications = DB::table('application as a')
            ->leftJoin('posts as p', 'a.post_id', '=', 'p.id')
            ->leftJoin('companies as c', 'a.company_id', '=', 'c.id')
            ->leftJoin('locations as l', 'c.location_id', '=', 'l.id')
            ->where('a.user_id', $validated['user_id'])
            ->where('a.status', '=', $validated['status'])
            ->select(
                'a.*',
                'p.title',
                'p.description',
                'c.company_name',
                'c.profile',
                'l.name as address'
            )
            ->get();

        $applications->each(function ($app) {
            $app->profile = env('CURRENT_URL') . Storage::url($app->profile);
        });

        if ($applications->count() > 0) {
            return ResponseHelper::success(
                $applications,
                "Successfully fetched applications",
                "success",
                200
            );
        }
        return ResponseHelper::error(
            "Failed to fetch application",
            "error",
            404
        );
    }

    public function selectApplicant(Request $request)
    {
        $validated = $request->validate([
            'company_id' => "required"
        ]);

        $applicantQuery = DB::table('application as a')
            ->join('users as u', 'a.user_id', 'u.id')
            ->join('posts as p', 'a.post_id', 'p.id')
            ->where('a.company_id', $validated['company_id'])
            ->select(
                'a.*',
                'u.nickname',
                'u.avatar as user_avatar',
                'p.title'
            );
        if ($request->has('status') && $request->status != "") {
            $applicantQuery->where('a.status', $request->status);
        }

        $applicant = $applicantQuery->get();

        if ($applicant->count() > 0) {
            $applicant->each(function ($app) {
                $app->user_avatar = env('CURRENT_URL') . Storage::url($app->user_avatar);
            });
            return ResponseHelper::success($applicant, "Successfully retreived appliants", "success", 200);
        }
        return ResponseHelper::error("Failed to fetch appliants", "error", 404);
    }

    public function applicantDetails(Request $request)
    {
        $validated = $request->validate(['id' => 'required|exists:application,id']);

        $applicant = DB::table("application as a")
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->join('users as ru', 'a.recruiter_id', '=', 'ru.id')
            ->join('posts as p', 'a.post_id', '=', 'p.id')
            ->join('locations as l', 'p.location_id', '=', 'l.id')
            ->join('companies as c', 'a.company_id', '=', 'c.id')
            ->join('employment_type as ept', 'p.employment_type_id', '=', 'ept.id')
            ->join('experience_level as epl', 'p.experience_level_id', '=', 'epl.id')
            ->join('resume as r', 'a.resume_id', '=', 'r.id')
            ->where('a.id', $validated['id'])
            ->select(
                'a.id',
                'a.status as status',
                'u.nickname as candidate',
                'u.id as candidate_id',
                'u.mobile as candidate_mobile',
                'u.email as candidate_email',
                'ru.nickname as recruiter',
                'u.avatar as candidate_profile',
                'p.title as job_title',
                'p.salary as salary_range',
                'l.name as location',
                'c.company_name',
                'c.profile as company_profile',
                'ept.title as employment_type',
                'epl.name as experience_level',
                'r.filename as file_name',
                'r.filepath as resume',
                'r.created_at as upload_at',
                'a.created_at as shipped_date',
                'p.created_at as posted_date'
            )
            ->first();

        if (Storage::disk('public')->exists($applicant->resume)) {
            $fileSizeBytes = Storage::disk('public')->size($applicant->resume);
            $applicant->filesize = round($fileSizeBytes / 1024, 2) . ' Kb';
        }

        $applicant->resume = env('CURRENT_URL') . Storage::url($applicant->resume);
        $applicant->candidate_profile = env('CURRENT_URL') . Storage::url($applicant->candidate_profile);
        $applicant->company_profile = env('CURRENT_URL') . Storage::url($applicant->company_profile);


        return ResponseHelper::success(
            $applicant,
            "Successfully retreived applicant",
            'success',
            200
        );
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:application,id',
            'status' => 'required|in:applied,shortlisted,interviewed,accepted,rejected'
        ]);


        try {
            DB::beginTransaction();

            $application = DB::table('application as a')
                ->join('users as u', 'a.user_id', '=', 'u.id')
                ->join('users as ru', 'a.recruiter_id', '=', 'ru.id')
                ->join('posts as p', 'a.post_id', '=', 'p.id')
                ->where('a.id', $validated['id'])
                ->select(
                    'a.*',
                    'u.nickname as candidate',
                    'ru.nickname as recruiter',
                    'p.title'
                )
                ->first();

            $notification = [
                'title' => "Hello {$application->candidate}, Please check your application.",
                'body' => "Your application({$application->title}) status has been updated to {$validated['status']}",
                'icon' => 'ic_launcher_foreground',
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'data' => [
                    'type' => 'application_status_update',
                    'application_id' => $validated['id']
                ]
            ];


            DB::table('application')
                ->where('id', $validated['id'])
                ->update(['status' => $validated['status']]);


            DB::table('notifications')
                ->insert([
                    "user_id" => $application->user_id,
                    "sender_id" => $application->recruiter_id,
                    "sender" => $application->recruiter,
                    "title" => $notification["title"],
                    "body" => $notification["body"],
                    "deeplink" => "-",
                    "receiver" => $application->candidate,
                    "status" => "unread",
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);

            DB::commit();

            $this->sendNotification(
                $this->getPosterFcmToken($application->user_id),
                $notification['title'],
                $notification['body']
            );


            return ResponseHelper::success(
                null,
                "Successfully updated status",
                "success",
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function acceptApplicant(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:application,id',
        ]);


        try {
            DB::beginTransaction();

            $application = DB::table('application as a')
                ->join('users as u', 'a.user_id', '=', 'u.id')
                ->join('users as ru', 'a.recruiter_id', '=', 'ru.id')
                ->join('posts as p', 'a.post_id', '=', 'p.id')
                ->where('a.id', $validated['id'])
                ->select(
                    'a.*',
                    'u.nickname as candidate',
                    'ru.nickname as recruiter',
                    'p.title'
                )
                ->first();

            $notification = [
                'title' => "Congratulation {$application->candidate}, you have been accepted by {$application->recruiter}.",
                'body' => "Your have been accepted to join {$application->recruiter} as {$application->title}",
                'icon' => 'ic_launcher_foreground',
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'data' => [
                    'type' => 'application_status_update',
                    'application_id' => $validated['id']
                ]
            ];


            DB::table('application')
                ->where('id', $validated['id'])
                ->update(['status' => 'accepted']);


            DB::table('notifications')
                ->insert([
                    "user_id" => $application->user_id,
                    "sender_id" => $application->recruiter_id,
                    "sender" => $application->recruiter,
                    "title" => $notification["title"],
                    "body" => $notification["body"],
                    "deeplink" => "-",
                    "receiver" => $application->candidate,
                    "status" => "unread",
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);

            DB::commit();

            $this->sendNotification(
                $this->getPosterFcmToken($application->user_id),
                $notification['title'],
                $notification['body']
            );

            return ResponseHelper::success(
                null,
                "Successfully updated status",
                "success",
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function rejectApplicant(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:application,id',
        ]);

        try {
            DB::beginTransaction();

            $application = DB::table('application as a')
                ->join('users as u', 'a.user_id', '=', 'u.id')
                ->join('users as ru', 'a.recruiter_id', '=', 'ru.id')
                ->join('posts as p', 'a.post_id', '=', 'p.id')
                ->where('a.id', $validated['id'])
                ->select(
                    'a.*',
                    'u.nickname as candidate',
                    'ru.nickname as recruiter',
                    'p.title',
                    'a.user_id',
                    'a.recruiter_id'
                )
                ->first();

            if (!$application) {
                return ResponseHelper::error("Application not found", "Failed", 404);
            }

            $interview = DB::table('interviews')
                ->where('application_id', '=', $application->id)
                ->first();

            if (!is_null($interview)) {
                DB::table('interviews')
                    ->where('application_id', '=', $application->id)
                    ->delete();
            }

            $notification = [
                'title' => "I'm sorry to hear that, {$application->candidate}, you have been rejected.",
                'body' => "Hello {$application->candidate}, your application [{$application->title}] has been rejected. Please try once again. Good luck!",
                'icon' => 'ic_launcher_foreground',
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'data' => [
                    'type' => 'application_status_update',
                    'application_id' => $validated['id']
                ]
            ];

            DB::table('application')
                ->where('id', $validated['id'])
                ->update(['status' => 'rejected']);

            DB::table('notifications')
                ->insert([
                    "user_id" => $application->user_id,
                    "sender_id" => $application->recruiter_id,
                    "sender" => $application->recruiter,
                    "title" => $notification["title"],
                    "body" => $notification["body"],
                    "deeplink" => "/application/{$validated['id']}",
                    "receiver" => $application->candidate,
                    "status" => "unread",
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);

            DB::commit();

            // Send notification if FCM token exists
            $fcmToken = $this->getPosterFcmToken($application->user_id);
            if ($fcmToken) {
                $this->sendNotification($fcmToken, $notification['title'], $notification['body']);
            }

            return ResponseHelper::success(null, "Successfully updated status", "success", 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error("An error occurred: " . $th->getMessage(), "error", 500);
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
