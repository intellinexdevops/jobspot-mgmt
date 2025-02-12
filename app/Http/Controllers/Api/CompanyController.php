<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = DB::table('companies as c')
            ->select(
                'c.id',
                'c.company_name',
                'c.profile',
                'c.follower'
            )
            ->paginate(10);
        return ResponseHelper::success(
            $companies,
            "Successfully retrieved companies",
            "success",
            200
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreCompanyRequest $request)
    {
        $data = $request->all();
        if (!isset($data["profile"])) {
            $data["profile"] = "upload/20250131231638.png";
        } else {
            if ($request->hasFile('profile')) {
                // Validate the avatar file
                $request->validate([
                    'profile' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max size 2MB
                ]);

                // Store the avatar file
                $avatarPath = $request->file('profile')->store('upload', 'public');

                // Save the file path to the database
                $data["profile"] = $avatarPath;
            }
        }

        $company = Company::create($data);

        return ResponseHelper::success(
            $company,
            "Successfully create a company.",
            "success",
            201
        );
    }
    public function selectByUser(Request $request)
    {
        $user_id = $request->user_id;

        $companies = DB::table('companies as c')
            ->join('industries as i', 'c.industry_id', '=', 'i.id')
            ->join('locations as l', 'location_id', '=', 'l.id')
            ->select('c.*', 'i.name as industry', 'l.name as location')
            ->where('c.user_id', $user_id)
            ->get();

        if ($companies->isEmpty()) {
            return ResponseHelper::error(
                "No company found for user_id {$user_id}",
                "404",
                404
            );
        }

        $companies->each(function ($company) {
            $company->profile = env('CURRENT_URL') . Storage::url($company->profile);
        });

        return ResponseHelper::success(
            $companies,
            "Successfully retrieved company",
            "success",
            200
        );
    }

    public function find(Request $request)
    {

        $id = $request->id;

        $company = DB::table("companies as c")
            ->join("users as u", "c.user_id", "=", "u.id")
            ->join("locations as l", "c.location_id", "=", "l.id")
            ->join("industries as i", "c.industry_id", "=", "i.id")
            ->where("c.id", $id)
            ->select('c.*', 'u.avatar as user_avatar', 'l.name', 'i.name as industry')
            ->first();
        return ResponseHelper::success(
            $company,
            "Successfully select a company",
            "success",
            200
        );
    }

    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'id' => "required|exists:companies,id"
        ]);

        $dataUpdate = [];

        if ($request->has('company_name')) {
            $dataUpdate['company_name'] = $request->company_name;
        }
        if ($request->has('industry_id')) {
            $dataUpdate['industry_id'] = $request->industry_id;
        }
        if ($request->has('location_id')) {
            $dataUpdate['location_id'] = $request->location_id;
        }
        if ($request->hasFile('profile')) {
            $request->validate([
                'profile' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max size 2MB
            ]);
            $avatarPath = $request->file('profile')->store('upload', 'public');

            $dataUpdate["profile"] = $avatarPath;
        }
        if ($request->has('website')) {
            $dataUpdate['website'] = $request->website;
        }
        if ($request->has('bio')) {
            $dataUpdate['bio'] = $request->bio;
        }

        if (!$dataUpdate) {
            return response()->json(['msg' => 'No data updated'], 400);
        }

        DB::table('companies')
            ->where('id', '=', $validated['id'])
            ->update($dataUpdate);


        return ResponseHelper::success(
            null,
            "Successfully update company",
            "success",
            200
        );
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $company = Company::find($id);

        if (!$company) {
            return ResponseHelper::error(
                "Company not found",
                "404",
                404
            );
        }

        $company->delete();

        return ResponseHelper::success(
            null,
            "Successfully delete a company",
            "success",
            200
        );
    }
}
