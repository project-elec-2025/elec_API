<?php

namespace App\Http\Controllers\admin;

use App\Models\Person;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EmployeeVote;

class personController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // return response()->json([
        //     'data' => $request->all()
        // ]);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employee_votes,id',
            'name' => 'required|string|max:255',
            'number_family' => 'required|string|max:255',
            'relation' => 'required|string|max:20',
            'type_election' => 'required|integer',
            'note' => 'nullable|string|max:255',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $person = Person::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Person created successfully.',
                // 'data' => $person->load(['employee', 'user'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create person.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Person updated successfully.',
        //     'data' => $request->all()
        // ], 200);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'sometimes|required|integer|exists:employee_votes,id',
            'name' => 'sometimes|required|string|max:255',
            'number_family' => 'sometimes|required|string|max:255',
            'relation' => 'sometimes|required|string|max:20',
            'type_election' => 'sometimes|required|integer',
            'note' => 'nullable|string|max:255',
            'user_id' => 'sometimes|required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $person = Person::find($id);

            if (!$person) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found.'
                ], 404);
            }

            $person->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Person updated successfully.',
                // 'data' => $person->load(['employee', 'user'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update person.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $person = Person::find($id);

            if (!$person) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found.'
                ], 404);
            }

            $person->delete();

            return response()->json([
                'success' => true,
                'message' => 'Person deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete person.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search people by name, number_family, or relation
     */
    // public function search(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'query' => 'required|string|min:2'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         $query = $request->input('query');
    //         $perPage = $request->input('per_page', 10);

    //         $people = Person::with(['employee', 'user'])
    //             ->where('name', 'LIKE', "%{$query}%")
    //             ->orWhere('number_family', 'LIKE', "%{$query}%")
    //             ->orWhere('relation', 'LIKE', "%{$query}%")
    //             ->orWhereHas('employee', function ($q) use ($query) {
    //                 $q->where('fullName', 'LIKE', "%{$query}%")
    //                     ->orWhere('card_number', 'LIKE', "%{$query}%");
    //             })
    //             ->orderBy('created_at', 'desc')
    //             ->paginate($perPage);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $people,
    //             'message' => 'Search results retrieved successfully.'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Search failed.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Get people by employee_id
     */
    public function findByEmpID($employeeId)
    {
        try {

            $employee = EmployeeVote::where('id', $employeeId)->select(['id', 'fullName', 'card_number', 'mobile', 'unit_office'])->first();

            $people = Person::where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'people' => $people,
                'employee' => $employee,
                'message' => 'People retrieved successfully for employee.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve people by employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get people by user_id
     */
    public function findByID($id)
    {
        try {


            $people = Person::where('id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($people->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'people' => $people,
                    'message' => 'People not found for employee.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'people' => $people,
                'message' => 'People retrieved successfully for employee.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve people by employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics about people
     */
    // public function getStatistics()
    // {
    //     try {
    //         $totalPeople = Person::count();
    //         $totalByTypeElection = Person::select('type_election', DB::raw('count(*) as count'))
    //             ->groupBy('type_election')
    //             ->get();
    //         $totalByRelation = Person::select('relation', DB::raw('count(*) as count'))
    //             ->groupBy('relation')
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'total_people' => $totalPeople,
    //                 'by_type_election' => $totalByTypeElection,
    //                 'by_relation' => $totalByRelation
    //             ],
    //             'message' => 'Statistics retrieved successfully.'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve statistics.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Bulk insert people
     */
    // public function bulkStore(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'people' => 'required|array',
    //         'people.*.employee_id' => 'required|integer|exists:employees,id',
    //         'people.*.name' => 'required|string|max:255',
    //         'people.*.number_family' => 'required|string|max:255',
    //         'people.*.relation' => 'required|string|max:20',
    //         'people.*.type_election' => 'required|integer',
    //         'people.*.note' => 'nullable|string|max:255',
    //         'people.*.user_id' => 'required|integer|exists:users,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $peopleData = $request->input('people');
    //         $createdPeople = [];

    //         foreach ($peopleData as $personData) {
    //             $person = Person::create($personData);
    //             $createdPeople[] = $person->load(['employee', 'user']);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'People created successfully in bulk.',
    //             'data' => $createdPeople
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create people in bulk.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
