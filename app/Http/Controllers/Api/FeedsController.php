<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feeds;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class FeedsController extends Controller
{
    public function index()
    {
        $data = Feeds::orderBy('created_at', 'desc')
            ->join('companies as c', 'feeds.company_id', '=', 'c.id')
            ->select(
                'feeds.id',
                'c.company_name as company_name',
                'c.profile as company_logo',
                'feeds.title',
                'feeds.description',
                'feeds.assets',
                'feeds.reaction_count',
                'feeds.comment_count',
                'feeds.created_at',
            )
            ->paginate(10);

        $data->getCollection()->transform(function ($feed) {
            // Decode JSON to an array or object
            $feed->assets = json_decode($feed->assets);

            // Concatenate CURRENT_URL with each asset URL if assets is an array
            if (is_array($feed->assets)) {
                $feed->assets = array_map(function ($asset) {
                    return env('CURRENT_URL') . Storage::url($asset); // Concatenate the URL
                }, $feed->assets);
            } elseif (is_string($feed->assets)) {
                // If assets is a single string, concatenate it directly
                $feed->assets = env('CURRENT_URL') . Storage::url($feed->assets);
            }

            return $feed;
        });

        return ResponseHelper::success($data, 'Feeds fetched successfully', 'Feeds fetched successfully', 200);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'assets' => 'nullable|array',
            'assets.*' => "image|mimes:jpg,png,jpeg,webp|max:2048"
        ]);

        // Handle the image upload
        $imagePaths = [];
        if ($request->hasFile('assets')) {
            foreach ($validated['assets'] as $image) {
                $imagePath = $image->store('upload', 'public');
                $imagePaths[] = $imagePath;
            }
        }

        $data = Feeds::create([
            "company_id" => $validated["company_id"],
            "title" => $validated["title"],
            "description" => $validated["description"],
            "assets" => json_encode($imagePaths),
            "reaction_count" => 0,
            "comment_count" => 0,
        ]);
        return ResponseHelper::success($data, 'Feeds created successfully', 'Feeds created successfully', 200);
    }

    public function update(Request $request, $id)
    {
        $data = Feeds::find($id)->update($request->all());
        return ResponseHelper::success($data, 'Feeds updated successfully', 'Feeds updated successfully', 200);
    }

    public function delete($id)
    {
        $data = Feeds::find($id)->delete();
        return ResponseHelper::success($data, 'Feeds deleted successfully', 'Feeds deleted successfully', 200);
    }

    public function find(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $data = Feeds::where('feeds.id', $request->id)
            ->join('companies', 'feeds.company_id', '=', 'companies.id')
            ->select(
                'feeds.*',
                'companies.company_name as company_name',
                'companies.profile as company_logo',
            )
            ->first();
        return ResponseHelper::success($data, 'Feeds fetched successfully', 'Feeds fetched successfully', 200);
    }
}
