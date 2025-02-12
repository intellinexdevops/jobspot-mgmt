<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use App\Helper\ResponseHelper;

class EmploymentTypeController extends Controller
{
    public function index()
    {
        $data = EmploymentType::all();
        return ResponseHelper::success($data, 'Employment Type List', "success", 200);
    }
}
