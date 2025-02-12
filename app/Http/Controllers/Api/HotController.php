<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hot;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class HotController extends Controller
{
    public function index(Request $request)
    {
        $hots = Hot::all();
        $hots->each(function ($hot) {
            $hot->image = env('CURRENT_URL') . Storage::url($hot->image);
        });
        return ResponseHelper::success($hots, 'Hot fetched successfully', 'success', 200);
    }
}
