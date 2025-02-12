<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index()
    {
        $data = Position::all();
        return ResponseHelper::success($data, 'Position List', "success", 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors(), "error", 400);
        }
        $data = Position::create($request->all());
        return ResponseHelper::success($data, 'Position Created', "success", 200);
    }
}
