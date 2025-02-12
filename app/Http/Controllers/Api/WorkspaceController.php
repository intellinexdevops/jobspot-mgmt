<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Helper\ResponseHelper;

class WorkspaceController extends Controller
{
    public function index()
    {
        $data = Workspace::all();
        if ($data->isEmpty()) {
            return ResponseHelper::error('No workspace found', 'No workspace found', 404);
        }
        return ResponseHelper::success($data, 'Workspace List', "success", 200);
    }
}
