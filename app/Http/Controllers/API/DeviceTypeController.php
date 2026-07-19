<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;

class DeviceTypeController extends Controller
{
    public function children($name)
    {
        $deviceType = DeviceType::where('name', $name)->first();

        if (! $deviceType) {
            return response()->json([]);
        }

        $children = DeviceType::where('parent_id', $deviceType->id)->get();

        return response()->json($children);
    }

    public function variants($modelName)
    {
        $deviceType = DeviceType::where('name', $modelName)->first();

        if (! $deviceType) {
            return response()->json([]);
        }

        $variants = DeviceType::where('parent_id', $deviceType->id)->get();

        return response()->json($variants);
    }
}
