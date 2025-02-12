<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * SELECT ALL
     * LOCATION
     */

    public function index()
    {
        $location = Location::all();

        return ResponseHelper::success(
            $location,
            "Successfully",
            "Success",
            200
        );
    }
}
