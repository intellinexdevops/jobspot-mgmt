<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    public function selectByUser(Request $request)
    {

        $validated = $request->validate([
            "user_id" => "required"
        ]);

        $resumes = DB::table("resume as r")
            ->where("user_id", "=", $validated["user_id"])
            ->get();

        if ($resumes->isEmpty()) {
            return ResponseHelper::error("No Resume Found", "Not Found", 404);
        }

        $resumes = $resumes->map(function ($res) {
            $filePath = $res->filepath;
            $res->file = env('CURRENT_URL') . Storage::url($res->filepath);

            // Get the file size if the file exists
            if (Storage::disk('public')->exists($filePath)) {
                $fileSizeBytes = Storage::disk('public')->size($filePath); // File size in bytes
                $res->fileSize = round($fileSizeBytes / 1024, 2);
            } else {
                $res->fileSize = null; // Handle missing files gracefully
            }

            return $res;
        });

        return ResponseHelper::success(
            $resumes,
            "Successfully retreived resume",
            "success",
            200
        );
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            "user_id" => "required",
            "filepath" => 'required|file|mimes:pdf|max:10240',
        ]);


        // Store the uploaded file and get its storage path
        $file = $request->file('filepath');
        $filepath = $file->store('upload', "public");

        // Extract filename and file type from the uploaded file
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filetype = $file->getClientOriginalExtension();
        $filesize = $file->getSize();

        // Insert data into the 'resume' table
        $id = DB::table("resume")->insertGetId([
            'user_id' => $validated['user_id'],
            'filename' => $filename,
            'filepath' => $filepath,
            'filetype' => $filetype,
            "created_at" => now(),
            "updated_at" => now()
        ]);
        if ($id) {
            return ResponseHelper::success(
                [
                    "id" => $id,
                    "user_id" => $validated["user_id"],
                    "filename" => $filename,
                    "file" => $filepath,
                ],
                "Successfully uploaded resume.",
                "success",
                201
            );
        }
        return ResponseHelper::error(
            "Failed to upload resume.",
            "error",
            500
        );
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            "resume_id" => "required|exists:resume,id" // Validate that the resume ID exists
        ]);

        // Fetch the resume entry from the database
        $resume = DB::table("resume")->where("id", $validated["resume_id"])->first();

        if (!$resume) {
            return ResponseHelper::error(
                "Resume not found.",
                "error",
                404
            );
        }

        $filePath = $resume->filepath;

        // Delete the file from storage if it exists
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        // Delete the record from the database
        $deleted = DB::table("resume")->where("id", $validated["resume_id"])->delete();

        if ($deleted) {
            return ResponseHelper::success(
                null,
                "Resume and associated file successfully deleted.",
                "success",
                200
            );
        }

        return ResponseHelper::error(
            "Failed to delete resume.",
            "error",
            500
        );
    }
}
