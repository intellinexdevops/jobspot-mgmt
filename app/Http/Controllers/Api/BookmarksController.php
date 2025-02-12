<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BookmarksController extends Controller
{
    public function index(Request $request)
    {
        $response = DB::table("bookmarks")
            ->join('users as u', 'user_id', '=', 'u.id')
            ->join('posts as p', 'post_id', '=', 'p.id')
            ->leftJoin("companies as c", "p.company_id", "=", "c.id")
            ->leftJoin("locations as l", "p.location_id", "=", "l.id")
            ->leftJoin("workspaces as w", "p.workspace_id", "=", "w.id")
            ->select(
                'p.id',
                'p.title',
                'p.status',
                'c.company_name',
                'l.name as location',
                "w.title as workspace"
            )->paginate(10);

        if ($response->isEmpty()) {
            return ResponseHelper::error('No bookmark found', 'error', 404);
        }

        return ResponseHelper::success(
            $response,
            'Successfully',
            'success',
            200
        );
    }

    public function create(Request $request)
    {
        $validat0r = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "post_id" => "required|exists:posts,id"
        ]);

        if ($validat0r->fails()) {
            return ResponseHelper::error($validat0r->errors(), "", 500);
        }
        $response = DB::table("bookmarks")
            ->insert($request->all());
        return ResponseHelper::success($response, "Created", "success", 201);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id|exists:bookmarks,user_id",
            "post_id" => "required|exists:bookmarks,post_id|exists:bookmarks,post_id"
        ]);

        if($validator->fails()) {
            return ResponseHelper::error($validator->errors(), "fail", 400);
        }
        $response = DB::table('bookmarks')
        ->where('post_id', $request->post_id)
        ->where('user_id', $request->user_id)
        ->delete();

        return ResponseHelper::success(
            $response,
            "Successfully delete a job from bookmark",
            "Success",
            200
        );

    }

    public function findByUSer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
        ]);
        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors(), "", 404);
        }
        $response = DB::table("bookmarks")
            ->join("users", "user_id", "=", "users.id")
            ->join("posts", "post_id", "=", "posts.id")
            ->leftJoin("companies", "posts.company_id", "=", "companies.id")
            ->leftJoin("locations", "posts.location_id", "=", "locations.id")
            ->leftJoin('career_skills', 'career_skills.post_id', '=', 'posts.id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'companies.company_name',
                'companies.profile as company_profile',
                'locations.name as location',
                'posts.salary',
                'posts.status',
                'posts.unit',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'posts.created_at as post_date'
            )
            ->where("users.id", $request->user_id)
            ->where("posts.status", "=", "active")
            ->groupBy(
                "posts.id",
                "posts.title",
                'posts.description',
                'companies.company_name',
                'companies.profile',
                'locations.name',
                'posts.salary',
                'posts.status',
                'posts.unit',
                'posts.created_at',
            )
            ->orderBy("post_date", "desc")
            ->paginate(10);

        if ($response->isEmpty()) {
            return ResponseHelper::error($validator->errors(), "Not Bookmark Found", 404);
        }

        $response->each(function($bookmark) {
            $bookmark->company_profile = env('CURRENT_URL') . Storage::url($bookmark->company_profile);
        });

        return ResponseHelper::success($response, "Successfully", "200", 200);
    }
}
