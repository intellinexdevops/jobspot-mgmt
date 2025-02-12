<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lang;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class LangController extends Controller
{
    public function index()
    {
        $langs = Lang::all();
        $langs->each(function ($lang) {
            $lang->icon = env('CURRENT_URL') . Storage::url($lang->icon);
        });
        return ResponseHelper::success(
            $langs,
            "語言列表",
            "success",
            200
        );
    }
}
