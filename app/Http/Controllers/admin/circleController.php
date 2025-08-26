<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class circleController extends Controller
{
    //
    public function getAllcircle()
    {
        $data = circle::latest('id');
        if ($data->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Empty circle data',
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'all circle data',
            'data' => $data
        ]);
    }

    public function add(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'circle_name' => 'required|string|max:255|unique:circles,circle_name',
        ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        circle::create(['circle_name' => $request->circle_name]);
        return response()->json([
            'success' => true,
            'message' => 'circle added succcessfully',

        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'circle_name' => 'required|string|max:255|unique:circles,circle_name,' . $id,
        ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        circle::find($id)->update(['circle_name' => $request->circle_name]);
        return response()->json([
            'success' => true,
            'message' => 'circle updated succcessfully',

        ], 200);
    }
}
