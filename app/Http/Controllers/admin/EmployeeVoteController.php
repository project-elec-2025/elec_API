<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\base;
use App\Models\EmployeeVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeVoteController extends Controller
{
    public function index()
    {
        return EmployeeVote::with('user')->get();
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'card_number' => 'required|string|unique:employee_votes,card_number',
            'unit_office' => 'required|string',
            'base' => 'required|string',
            'base_id' => 'required|numeric',
            'circle_id' => 'required|numeric',
            // 'is_election' => 'boolean',
            'note' => 'nullable|string',
            // 'datetime' => 'nullable|date',
            //'user_id' => 'required|exists:users,id'
        ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        $vote = new EmployeeVote();

        $vote->fullName = $request->fullName;
        $vote->mobile = $request->mobile;
        $vote->address = $request->address;
        $vote->card_number = $request->card_number;
        $vote->unit_office = $request->unit_office;
        $vote->base = $request->base;
        $vote->base_id = $request->base_id;
        $vote->circle_id = $request->circle_id;
        // $vote->is_election=$request->is_election;
        $vote->note = $request->note;
        // $vote->datetime=$request->datetime;

        $vote->save();
        return response()->json([
            'success' => true,
            'message' => 'Add Employee Vote successfully'
        ]);
        // return response()->json($vote, 201);
    }
    public function update(Request $request, $id)
    {


        $validated = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'card_number' => 'required|string|unique:employee_votes,card_number,' . $id,
            'unit_office' => 'required|string',
            'base' => 'required|string',
            'base_id' => 'required|numeric',
            'circle_id' => 'required|numeric',
            // 'is_election' => 'boolean',
            'note' => 'nullable|string',
            // 'datetime' => 'nullable|date',
            //'user_id' => 'required|exists:users,id'
        ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        $vote = EmployeeVote::find($id);

        $vote->fullName = $request->fullName;
        $vote->mobile = $request->mobile;
        $vote->address = $request->address;
        $vote->card_number = $request->card_number;
        $vote->unit_office = $request->unit_office;
        $vote->base = $request->base;
        $vote->base_id = $request->base_id;
        $vote->circle_id = $request->circle_id;
        // $vote->is_election=$request->is_election;
        $vote->note = $request->note;
        // $vote->datetime=$request->datetime;

        $vote->save();
        return response()->json([
            'success' => true,
            'message' => 'Update Employee Vote successfully'
        ]);
        // return response()->json($vote, 201);
    }

    public function vote(Request $request, $id)
    {

        $validated = Validator::make($request->all(), [

            // 'is_election' => 'boolean',
            'note' => 'nullable|string',

        ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        $vote = EmployeeVote::find($id);


        $vote->is_election = true;
        $vote->note = $request->note;
        $vote->datetime = now()->format('Y-m-d H:i:s');
        $vote->user_id = Auth::user()->id;

        $vote->save();
        return response()->json([
            'success' => true,
            'message' => ' Employee Vote successfully'
        ]);
        // return response()->json($vote, 201);
    }
    public function AddNoteForEmployee(Request $request, $id)
    {

        $validated = Validator::make($request->all(), [
            'note' => 'nullable|string',
        ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        $vote = EmployeeVote::find($id);


        $vote->note = $request->note;
        $vote->datetime = now()->format('Y-m-d H:i:s');
        $vote->user_id = Auth::user()->id;

        $vote->save();
        return response()->json([
            'success' => true,
            'message' => 'Add Note Employee  successfully'
        ]);
        // return response()->json($vote, 201);
    }
    public function findEmployee($Search)
    {


        $data = EmployeeVote::where('card_number', $Search)
            ->orWhere('mobile', $Search)
            ->orWhere('fullName', 'like', $Search . '%') // Starts with - better performance
            ->first();
        if ($data) {

            if ($data->base_id != Auth::user()->base_id) {
                return response()->json([
                    'success' => true,
                    'message' => 'found,This employee is not on this base',
                    'base_name' => $data->base
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'data Employee',
                'data' => $data
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'data not found',
            ]);
        }
    }



    public function listNoteVote()
    {
        $data = EmployeeVote::where('base_id', Auth::user()->base_id)
            ->where('is_election', '=', false)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List of those who did not vote',
            'data' => $data,
        ], 200);
    }



    public function voteAllStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'all' => EmployeeVote::where('base_id', Auth::user()->base_id)->count(),
                'voted' => EmployeeVote::where('base_id', Auth::user()->base_id)->where('is_election', true)->count(),
                'not_voted' => EmployeeVote::where('base_id', Auth::user()->base_id)->where('is_election', false)->count()
            ]
        ], 200);
    }



    public function getAllVoteStats()
    {


        // Get bases with their names and IDs
        $bases = Base::select(['id', 'base_name'])
            ->orderBy('base_name')  // Order by name instead of ID
            ->get();

        // Get all vote counts in a single query
        $voteCounts = EmployeeVote::selectRaw('
                    base_id,
                    COUNT(*) as total,
                    SUM(is_election = 1) as voted,
                    SUM(is_election = 0) as not_voted
                    ')
            ->groupBy('base_id')
            ->get()
            ->keyBy('base_id');

        // Build the result array with base_name as key
        $result = [];
        foreach ($bases as $base) {
            $counts = $voteCounts->get($base->id, (object) [
                'total' => 0,
                'voted' => 0,
                'not_voted' => 0
            ]);

            $result[$base->base_name] = [  // Using base_name as the array key
                'base_id' => $base->id,    // Include ID in the result if needed
                'all' => $counts->total,
                'voted' => $counts->voted,
                'not_voted' => $counts->not_voted
            ];
        }


        return response()->json([
            'success' => true,
            'data' => $result,
            'total' => EmployeeVote::count(),
            'total_vote' => EmployeeVote::where('is_election', true)->count(),
            'total_note_vote' => EmployeeVote::where('is_election', false)->count(),

        ], 200);
    }



    public function getAllNotVote()
    {
        // Get paginated non-voting employees grouped by base
        $notVotedByBase = Base::with(['employeeVotes' => function ($query) {
            $query->where('is_election', false)
                ->select('id', 'fullName', 'mobile', 'base_id', 'is_election');
        }])
            ->select('id', 'base_name')
            ->orderBy('base_name')
            ->paginate(100);

        // Transform the data structure
        $formattedData = $notVotedByBase->map(function ($base) {
            return [
                // 'base_id' => $base->id,
                'base_name' => $base->base_name,
                //   'not_voted_count' => $base->employeeVotes->count(),
                'list' => $base->employeeVotes->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'fullName' => $employee->fullName,
                        'mobile' => $employee->mobile,
                        'unit_office' => $employee->unit_office,
                        'note' => $employee->note
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' =>  $formattedData,
            'pagination' => [
                'total' => $notVotedByBase->total(),
                'per_page' => $notVotedByBase->perPage(),
                'current_page' => $notVotedByBase->currentPage(),
                'last_page' => $notVotedByBase->lastPage()

            ]
        ], 200);
    }
}
