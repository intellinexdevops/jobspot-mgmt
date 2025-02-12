<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmploymentSize;
use App\Helper\ResponseHelper;

class EmployeeSizeController extends Controller
{
    public function index()
    {
        $employmentSizes = EmploymentSize::all();
        return ResponseHelper::success($employmentSizes, "Employment sizes fetched successfully", "employment_sizes", 200);
    }
}
