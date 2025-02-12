<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExperienceLevel;
use App\Helper\ResponseHelper;

class ExperienceLevelController extends Controller
{
    public function index()
    {
        $data = ExperienceLevel::all();
        return ResponseHelper::success(
            $data,
            "Successfully",
            "success",
            200
        );
    }
}
