<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{

    public function index()
    {
        $data = DB::table('skills')->paginate(10);
        return ResponseHelper::success($data, 'Skill List', "success", 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);
    }
}
