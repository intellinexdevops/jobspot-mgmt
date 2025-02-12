<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Handle the image upload
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'image' => "required|image|mimes:jpeg,jpg,png|max:2048"
        ]);

        $file = $request->file("image");

        if ($file->isValid()) {

            $filename = now()->format("YmdHis") . "." . $file->getClientOriginalExtension();
            $path = $file->storeAs('upload', $filename, "public");

            return response()->json([
                "code" => 1,
                "msg" => "Image uploaded successfully.",
                "status" => "success",
                "data" => Storage::url($path)
            ], 201);
        } else {

            return response()->json([
                "code" => 0,
                "msg" => "Failed to upload images.",
                "status" => "error",
                "data" => null
            ], 500);
        }
    }
}
