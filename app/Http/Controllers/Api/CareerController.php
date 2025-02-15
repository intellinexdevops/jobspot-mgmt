<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Api\CareerSkill;
use App\Http\Requests\Api\StoreCareerRequest;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    public function index(Request $req)
    {
        if (isset($req->user_id)) {
            $userid = $req->user_id;
            $bookmarks = DB::table('bookmarks')
                ->where('user_id', '=', $userid)
                ->pluck('post_id')
                ->toArray();
        }

        $bookmarkedCheck = empty($bookmarks)
            ? '0 as saved'
            : 'IF(fa_posts.id IN (' . implode(',', $bookmarks) . '), 1, 0) as saved';

        try {

            $careers = DB::table('posts')
                ->join('companies', 'posts.company_id', '=', 'companies.id')
                ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
                ->join('locations', 'posts.location_id', '=', 'locations.id')
                ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
                ->join('industries', 'companies.industry_id', '=', 'industries.id')
                ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
                ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
                ->leftJoin('experience_level', 'posts.experience_level_id', "=", "experience_level.id")
                ->select(
                    'posts.id',
                    'posts.title',
                    'posts.description',
                    'posts.salary',
                    'posts.unit',
                    DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                    'companies.company_name as company_name',
                    'companies.profile as company_profile',
                    'industries.name as industry_name',
                    'locations.name as location_name',
                    'workspaces.title as workspace',
                    'employment_type.title as employment_type',
                    'posts.status',
                    'experience_level.name as experience_level',
                    'posts.created_at',
                    'posts.updated_at',
                    DB::raw($bookmarkedCheck)
                )
                ->where('posts.status', 'active')
                ->groupBy(
                    'posts.id'
                )
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $careers->each(function ($career) {
                $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
            });

            return ResponseHelper::success($careers, 'Careers fetched successfully', '200', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), '500', 500);
        }
    }

    public function create(StoreCareerRequest $request)
    {
        DB::beginTransaction();
        try {
            $career = Career::create($request->validated());

            $skills = $request->skills;
            // String to array
            $skills = explode(',', $skills);
            foreach ($skills as $skill) {
                CareerSkill::create([
                    'post_id' => $career->id,
                    'skill_id' => $skill,
                ]);
            }

            DB::commit();
            return ResponseHelper::success($career, 'Career created successfully', '200', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage(), '500', 500);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors(), '400', 400);
        }

        try {
            $career = Career::find($request->id);
            $career->delete();
            return ResponseHelper::success(1, 'Career deleted successfully', '200', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), '500', 500);
        }
    }

    public function publish(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:posts,id',
        ]);

        try {
            $career = Career::find($validated['id']);
            $career->status = 'active';
            $career->save();
            return ResponseHelper::success(1, 'Career published successfully', '200', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), '500', 500);
        }
    }
    public function draft(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:posts,id',
        ]);

        try {
            $career = Career::find($validated['id']);
            $career->status = 'draft';
            $career->save();
            return ResponseHelper::success(1, 'Career draft successfully', '200', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), '500', 500);
        }
    }

    public function find(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required'
        ]);

        $id = $validated['id'];

        if (isset($request->user_id)) {

            $applications = DB::table("application as a")
                ->where("user_id", "=", $request->user_id)
                ->pluck('post_id') // Retrieve post IDs only
                ->toArray();

            $bookmarks = DB::table('bookmarks as b')
                ->where('user_id', '=', $request->user_id)
                ->pluck('post_id')
                ->toArray();
        }

        $appliedCheck = empty($applications)
            ? '0 as applied'
            : 'IF(fa_posts.id IN (' . implode(',', $applications) . '), 1, 0) as applied';

        $bookmarkedCheck = empty($bookmarks)
            ? '0 as saved'
            : 'IF(fa_posts.id IN (' . implode(',', $bookmarks) . '), 1, 0) as saved';

        $career = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->join('users', 'companies.user_id', '=', 'users.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', "posts.experience_level_id", "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                'posts.requirement',
                'posts.facilities',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.id as company_id',
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'companies.follower as followers',
                'companies.user_id as author_id',
                'users.push_token as fcm',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
                DB::raw($appliedCheck),
                DB::raw($bookmarkedCheck)
            )
            ->where('posts.id', '=', $id)
            ->groupBy('posts.id')
            ->first();

        $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        $postCount = DB::table('posts')
            ->where('company_id', '=', $career->company_id)
            ->count();
        $career->total_posts = $postCount;


        return ResponseHelper::success(
            $career,
            'Sucessfully Retreived Career.',
            'success',
            200
        );
    }

    public function selectParttime()
    {

        $response = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', 'posts.experience_level_id', "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.status', 'active')
            ->where('posts.employment_type_id', '=', '2')
            ->groupBy('posts.id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($response->isEmpty()) {
            return ResponseHelper::error(
                "No active part-time posts found.",
                "error",
                404
            );
        }

        $response->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });

        return ResponseHelper::success(
            $response,
            "Successfully",
            "success",
            200
        );
    }

    public function selectFulltime()
    {

        $response = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', 'posts.experience_level_id', "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.status', 'active')
            ->where('posts.employment_type_id', '=', '1')
            ->groupBy('posts.id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        if ($response->isEmpty()) {
            return ResponseHelper::error(
                "No active full-time posts found.",
                "error",
                404
            );
        }
        $response->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });
        return ResponseHelper::success(
            $response,
            "Successfully",
            "success",
            200
        );
    }
    public function selectRemote()
    {

        $response = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', 'posts.experience_level_id', "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.status', 'active')
            ->where('posts.workspace_id', '=', '3')
            ->groupBy('posts.id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($response->isEmpty()) {
            return ResponseHelper::error(
                "No active remote posts found.",
                "error",
                404
            );
        }
        $response->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });
        return ResponseHelper::success(
            $response,
            "Successfully",
            "success",
            200
        );
    }

    public function search(Request $request)
    {
        $query = DB::table("posts as p");
        if ($request->has("title")) {
            $query
                ->join("companies as c", "p.company_id", "=", "c.id")
                ->join('workspaces as w', 'p.workspace_id', '=', 'w.id')
                ->join('locations as l', 'p.location_id', '=', 'l.id')
                ->join('industries as i', 'c.industry_id', '=', 'i.id')
                ->leftJoin('career_skills as cs', 'p.id', '=', 'cs.post_id')
                ->join('employment_type as ept', 'p.employment_type_id', '=', 'ept.id')
                ->leftJoin('experience_level as el', "p.experience_level_id", "=", "el.id")
                ->leftJoin('skills as s', 'cs.skill_id', '=', 's.id')
                ->where("p.title", "like", "%" . $request->get("title") . "%")
                // ->orWhere('description', 'like', '%' . $request->get('title') . '%')
                ->where('p.status', '=', 'active');
        }
        $result = $query->select(
            'p.id',
            'p.title',
            'p.description',
            'p.salary',
            'p.unit',
            'p.requirement',
            'p.facilities',
            'c.follower',
            DB::raw('GROUP_CONCAT(fa_s.title SEPARATOR ", ") as skills'),
            'c.company_name',
            'w.title as workspace',
            'l.name as location_name',
            'c.profile as company_profile',
            'i.name as industry_name',
            'p.status',
            'ept.title as employment_type',
            'el.name as experience_level',
            'p.created_at',
            'p.updated_at',
        )
            ->groupBy('p.id')
            ->paginate(10);
        $result->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });
        return ResponseHelper::success(
            $result,
            "Successfully",
            "success",
            200
        );
    }
    public function selectAll(Request $request)
    {
        $validated = $request->validate([
            'company_id' => "required:exists:companies,id"
        ]);
        $careers = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', "posts.experience_level_id", "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                'posts.requirement',
                'posts.facilities',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'companies.follower as followers',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.company_id', '=', $validated['company_id'])
            ->groupBy('posts.id')
            ->orderBy('posts.created_at', 'desc')
            ->paginate(10);

        $careers->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });

        if ($careers->isEmpty()) {
            return ResponseHelper::error(
                'No content',
                'failed',
                404
            );
        }

        return ResponseHelper::success(
            $careers,
            'Sucessfully Retreived Career.',
            'success',
            200
        );
    }
    public function selectOpen(Request $request)
    {
        $validated = $request->validate([
            'company_id' => "required:exists:companies,id"
        ]);

        $careers = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', "posts.experience_level_id", "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                'posts.requirement',
                'posts.facilities',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'companies.follower as followers',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.company_id', '=', $validated['company_id'])
            ->where('posts.status', '=', 'active')
            ->groupBy('posts.id')
            ->paginate(10);

        $careers->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });
        if ($careers->isEmpty()) {
            return ResponseHelper::error(
                'No content',
                'failed',
                404
            );
        }

        return ResponseHelper::success(
            $careers,
            'Sucessfully Retreived Career.',
            'success',
            200
        );
    }

    public function selectDraft(Request $request)
    {
        $validated = $request->validate([
            'company_id' => "required:exists:companies,id"
        ]);

        $career = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', "posts.experience_level_id", "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                'posts.requirement',
                'posts.facilities',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'companies.follower as followers',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.company_id', '=', $validated['company_id'])
            ->where('posts.status', '=', 'draft')
            ->groupBy('posts.id')
            ->paginate(10);

        $career->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });
        if ($career->isEmpty()) {
            return ResponseHelper::error(
                'No content',
                'failed',
                404
            );
        }

        return ResponseHelper::success(
            $career,
            'Sucessfully Retreived Career.',
            'success',
            200
        );
    }
    public function selectClosed(Request $request)
    {
        $validated = $request->validate([
            'company_id' => "required:exists:companies,id"
        ]);

        $career = DB::table('posts')
            ->join('companies', 'posts.company_id', '=', 'companies.id')
            ->join('workspaces', 'posts.workspace_id', '=', 'workspaces.id')
            ->join('locations', 'posts.location_id', '=', 'locations.id')
            ->join('employment_type', 'posts.employment_type_id', '=', 'employment_type.id')
            ->join('industries', 'companies.industry_id', '=', 'industries.id')
            ->leftJoin('career_skills', 'posts.id', '=', 'career_skills.post_id')
            ->leftJoin('skills', 'career_skills.skill_id', '=', 'skills.id')
            ->leftJoin('experience_level', "posts.experience_level_id", "=", "experience_level.id")
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.salary',
                'posts.unit',
                'posts.requirement',
                'posts.facilities',
                DB::raw('GROUP_CONCAT(fa_skills.title SEPARATOR ", ") as skills'),
                'companies.company_name as company_name',
                'companies.profile as company_profile',
                'companies.follower as followers',
                'industries.name as industry_name',
                'locations.name as location_name',
                'workspaces.title as workspace',
                'employment_type.title as employment_type',
                'posts.status',
                'experience_level.name as experience_level',
                'posts.created_at',
                'posts.updated_at',
            )
            ->where('posts.company_id', '=', $validated['company_id'])
            ->where('posts.status', '=', 'inactive')
            ->groupBy('posts.id')
            ->paginate(10);

        $career->each(function ($career) {
            $career->company_profile = env('CURRENT_URL') . Storage::url($career->company_profile);
        });
        if ($career->isEmpty()) {
            return ResponseHelper::error(
                'No content',
                'failed',
                404
            );
        }

        return ResponseHelper::success(
            $career,
            'Sucessfully Retreived Career.',
            'success',
            200
        );
    }
}
