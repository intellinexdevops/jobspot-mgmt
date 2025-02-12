<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\Builder\Identity;

class IndustryController extends Controller
{
    public function index()
    {
        $data = DB::table('industries')
            ->leftJoin('sub_industries', 'industries.id', '=', 'sub_industries.industry_id')
            ->select(
                'industries.*',
                DB::raw('GROUP_CONCAT(fa_sub_industries.name SEPARATOR ",") as sub_industries'),
            )
            ->groupBy('industries.id')
            ->paginate(10);

        return ResponseHelper::success($data, "Retreived successfully", "success", 200);
    }

    public function search(Request $request)
    {
        $query = Industry::query();

        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        $data = $query->get();
        return ResponseHelper::success($data, "Retrieved successfully", "success", 200);
    }
}
