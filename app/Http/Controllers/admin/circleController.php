<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\circle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class circleController extends Controller
{
    //
    public function getAllcircle()
    {
        $data = circle::orderby('id', 'desc')->get();
        if ($data->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Empty circle data',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'all circle data',
            'AllData' => $data,

        ]);
    }
    public function circelFindById($id)
    {
        $data = circle::find($id);

        return $data;
        if ($data->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Empty circle data',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'circle data',
            'AllData' => $data
        ]);
    }
    public function add(Request $request)
    {
        $request->validate([
            'circle_name' => 'required|string|max:255|unique:circles,circle_name',
        ]);
        // if ($validated->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validation error',
        //         'errors' => $validated->errors()
        //     ], 422);
        // }

        // , 'created_at' => Carbon::now()

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

    public function delete($id)
    {
        $data = circle::find($id);

        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'circle delete succcessfully',

        ], 200);
    }
}
