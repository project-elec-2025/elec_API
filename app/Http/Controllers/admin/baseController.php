<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\base;
use App\Models\EmployeeVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class baseController extends Controller
{
    //
    public function getAllBase()
    {
        $data = base::with('cirlce:id,circle_name')->get();
        if ($data->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Empty base data',
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'all base data',
            'data' => $data
        ]);
    }
    //
    public function getBaseById($id)
    {
        $data = base::with('cirlce:id,circle_name')->where('id', $id)->get();
        if ($data->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Empty base data',
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'find base data',
            'data' => $data
        ]);
    }

    public function getBaseByCircle($circle_id)
    {
        $data = base::with('cirlce:id,circle_name')->where('circle_id', $circle_id)->get();
        if ($data->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Empty base data',
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => ' base data',
            'data' => $data
        ]);
    }

    public function add(Request $request)
    {


        $request->validate([
            'base_name' => 'required|string|max:255|unique:bases,base_name',
            'circle_id' => 'required|numeric'
        ]);


        base::create([
            'base_name' => $request->base_name,
            'circle_id' => $request->circle_id
        ]);
        return response()->json([
            'success' => true,
            'message' => 'base added succcessfully',

        ], 201);
    }

    public function update(Request $request, $id)
    {
        // return response()->json([
        //     'success' => true,
        //     'message' => $request->all(),

        // ], 200);
        $request->validate([
            'base_name' => 'required|string|max:255|unique:bases,base_name,' . $id,
            'circle_id' => 'required|numeric'
        ]);
        // if ($validated->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validation error',
        //         'errors' => $validated->errors()
        //     ], 422);
        // }

        base::find($id)->update(['base_name' => $request->base_name, 'circle_id' => $request->circle_id]);
        return response()->json([
            'success' => true,
            'message' => 'base updated succcessfully',

        ], 200);
    }

    public function fix()
    {
        $bases = base::select(['id', 'base_name'])->get();
        // $employees = EmployeeVote::limit(1000)-> where('base_id', '!=', null)->get();
        $employees = EmployeeVote::whereNull('base_id')
            ->select(['id', 'base', 'base_id']) // Only select necessary columns
            ->limit(1000)
            ->get();

        $num = 0;
        foreach ($bases as $base) {
            foreach ($employees as $employee) {

                if ($employee->base === $base->base_name) {
                    $data = EmployeeVote::find($employee->id);

                    $data->base_id = $base->id;

                    $data->save();
                    ++$num;
                    // response()->json(['num' => $num]);
                }
            }
        }

        return response()->json([
            'bases' =>   'bases',
            // 'employee' => $employees
        ]);
    }
}
