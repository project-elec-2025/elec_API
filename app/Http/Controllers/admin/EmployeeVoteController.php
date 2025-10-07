<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\base;
use App\Models\circle;
use App\Models\EmployeeVote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class EmployeeVoteController extends Controller
{

    public function indexx()
    {


        // return response()->json([
        //     'success' => true,
        //     'message' => 'List of those who did not vote',
        //     'data' => 'null'

        // ], 200);

        // $user_id = $request->userid;
        // $user = User::find($user_id);

        if (Auth::user()->role == "admin") {
            $data = EmployeeVote::with(['cirlces', 'base'])

                ->get();

            return response()->json([
                'success' => true,
                'message' => 'List of those who did not vote',
                'data' => $data,
                // 'pagination' => [
                //     'current_page' => $data->currentPage(),
                //     'last_page' => $data->lastPage(),
                //     'per_page' => $data->perPage(),
                //     'total' => $data->total(),
                // ]
            ], 200);
        } else {

            $data = EmployeeVote::select(['id', 'fullName', 'card_number', 'mobile', 'address', 'unit_office', 'is_election', 'note'])
                ->where('base_id', Auth::user()->base_id)
                // ->where('card_number', $request->get('search'))
                ->get();
            return response()->json([
                'success' => true,
                'message' => 'List of those who did not vote',
                'data' => $data,

            ], 200);
        }
    }

    public function index(Request $request)
    {


        // return response()->json([
        //     'success' => true,
        //     'message' => 'List of those who did not vote',
        //     'data' => 'null'

        // ], 200);

        // $user_id = $request->userid;
        // $user = User::find($user_id);
        $perPage = $request->get('per_page', 10); // default to 10
        $page = $request->get('page', 1);

        if (Auth::user()->role == "admin") {
            $data = EmployeeVote::with(['cirlces', 'base'])

                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'List of those who did not vote',
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ], 200);
        } else {

            $data = EmployeeVote::select(['id', 'fullName', 'card_number', 'mobile', 'address', 'unit_office', 'is_election', 'note'])
                ->where('base_id', Auth::user()->base_id)
                // ->where('card_number', $request->get('search'))
                ->get();
            return response()->json([
                'success' => true,
                'message' => 'List of those who did not vote',
                'data' => $data,

            ], 200);
        }
    }


    public function show($id)
    {
        $vote = EmployeeVote::find($id);
        if (!$vote) {
            return response()->json([
                'success' => false,
                'message' => 'Employee Vote not found',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Employee Vote found',
            'data' => $vote
        ]);
    }

    //allhatw
    // In your Laravel controller method
    public function allhatw(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');

        $query = EmployeeVote::select(['id', 'fullName', 'card_number', 'mobile', 'address', 'unit_office', 'is_election', 'note'])
            ->where('base_id', Auth::user()->base_id)
            ->where('is_election', true);

        // Add search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullName', 'like', "%{$search}%")
                    ->orWhere('card_number', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'List of those who voted',
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ], 200);
    }
    public function allNahatw(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');

        $query = EmployeeVote::select(['id', 'fullName', 'card_number', 'mobile', 'address', 'unit_office', 'is_election', 'note'])
            ->where('base_id', Auth::user()->base_id)
            ->where('is_election', false);

        // Add search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullName', 'like', "%{$search}%")
                    ->orWhere('card_number', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'List of those who voted',
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ], 200);
    }
    // In your EmployeeController
    public function search(Request $request)
    {
        $search = $request->input('q');

        $query = EmployeeVote::select(['id', 'fullName', 'card_number', 'mobile', 'is_election'])
            ->where('base_id', Auth::user()->base_id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullName', 'like', "%{$search}%")
                    ->orWhere('card_number', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $results = $query->limit(50)->get();

        return response()->json([
            'success' => true,
            'message' => 'Search results',
            'data' => $results
        ], 200);
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
            // 'base' => 'required|string',
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

            'is_election' => 'boolean',
            'note' => 'nullable|string',

        ]);
        // if ($validated->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validation error',
        //         'errors' => $validated->errors()
        //     ], 422);
        // }

        $vote = EmployeeVote::find($id);

        if ($request->has('is_election') && $request->is_election !== null) {
            $vote->is_election = $request->is_election;
        }
        $vote->note = $request->note;
        $vote->datetime = now()->format('Y-m-d H:i:s');
        $vote->user_id = Auth::user()->id;

        $vote->save();
        return response()->json([
            'success' => true,
            'message' => ' Employee Vote successfully',
            'data' => $request->is_election
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

        // return response()->json([
        //     'success' => true,
        //     'message' => 'data Employee',
        //     'data' => $Search
        // ]);
        $data = EmployeeVote::select(['id', 'fullName', 'card_number', 'mobile', 'address', 'unit_office', 'is_election', 'note', 'base', 'base_id'])
            ->where('card_number', $Search)
            // ->orWhere('mobile', $Search)
            // ->orWhere('fullName', 'like', $Search . '%') // Starts with - better performance
            ->first();
        if ($data) {

            if ($data->base_id != Auth::user()->base_id) {
                return response()->json([
                    'success' => true,
                    'message' => 'lam_bnkaya_nya',
                    'base_name' => $data->base
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'data_Employee',
                    'data' => $data
                ]);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => 'not_found',
            ]);
        }
    }

    public function findEmployeeByNameAndMobile($Search)
    {
        $data = EmployeeVote::select([
            'id',
            'fullName',
            'card_number',
            'mobile',
            'address',
            'unit_office',
            'is_election',
            'note',
            'base',
            'base_id'
        ])
            ->where(function ($query) use ($Search) {
                $query->where('mobile', 'like', $Search . '%')
                    ->orWhere('fullName', 'like', $Search . '%');
            })->where('base_id', '!=', Auth::user()->base_id)
            ->orWhere('card_number', 'like', $Search . '%')
            ->limit(20)
            ->get();

        if ($data->isNotEmpty()) {
            // check if ANY employee has a different base_id
            // $differentBase = $data->firstWhere('base_id', '!=', Auth::user()->base_id);

            // if ($differentBase) {
            //     return response()->json([
            //         'success' => true,
            //         'message' => 'lam_bnkaya_nya',
            //         // 'base_name' => $differentBase->base
            //     ]);
            // }

            return response()->json([
                'success' => true,
                'message' => 'data_Employee',
                'data' => $data
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'not_found',
        ]);
    }


    public function listNoteVote(Request $request)
    {


        $perPage = $request->get('per_page', 10); // default to 10
        $page = $request->get('page', 1);

        if (Auth::user()->role === "admin") {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('is_election', false)
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('base_id', Auth::user()->base_id)
                ->where('is_election', false)
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'success' => true,
            'message' => 'List of those who did not vote',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ], 200);
    }
    public function listNoteVoteTest()
    {


        // $perPage = $request->get('per_page', 10); // default to 10
        // $page = $request->get('page', 1);

        // if (Auth::user()->role === "admin") {
        //     $data = EmployeeVote::with(['cirlces', 'base'])
        //         ->where('is_election', false)
        //         ->paginate($perPage, ['*'], 'page', $page);
        // } else {
        $data = EmployeeVote::with(['cirlces', 'base'])
            // ->where('base_id', Auth::user()->base_id)
            ->where('is_election', false)->get();
        // ->paginate($perPage, ['*'], 'page', $page);
        // }

        return response()->json([
            'success' => true,
            'message' => 'List of those who did not vote',
            'data' => $data,

        ], 200);
    }
    public function listVoteByBaseId(Request $request)
    {


        $perPage = $request->get('per_page', 10); // default to 10
        $page = $request->get('page', 1);
        $base_id = $request->get('baseId');

        if (Auth::user()->role === "admin") {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('base_id', $base_id)
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('base_id', Auth::user()->base_id)
                // ->where('base_id', $base_id)
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'success' => true,
            'message' => 'List of those who did not vote',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ], 200);
    }

    // listVoteByCircleId
    public function listVoteByCircleId(Request $request)
    {


        $perPage = $request->get('per_page', 10); // default to 10
        $page = $request->get('page', 1);
        $circle_id = $request->get('circleId');

        if (Auth::user()->role === "admin") {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('circle_id', $circle_id)
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('circle_id', Auth::user()->circle_id)
                // ->where('base_id', $base_id)
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'success' => true,
            'message' => 'List of those who did not vote',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ], 200);
    }
    public function listVote(Request $request)
    {


        $perPage = $request->get('per_page', 10); // default to 10
        $page = $request->get('page', 1);

        if (Auth::user()->role === "admin") {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('is_election', true)
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $data = EmployeeVote::with(['cirlces', 'base'])
                ->where('base_id', Auth::user()->base_id)
                ->where('is_election', true)
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'success' => true,
            'message' => 'List of those who did not vote',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ], 200);
    }
    // chawder
    public function voteAllStats()
    {


        return response()->json([
            'success' => true,
            'data' => EmployeeVote::selectRaw("
                        COUNT(*) as all_votes,
                        SUM(CASE WHEN is_election = 1 THEN 1 ELSE 0 END) as voted,
                        SUM(CASE WHEN is_election = 0 THEN 1 ELSE 0 END) as not_voted
                    ")
                ->where('base_id', Auth::user()->base_id)
                ->first()
        ], 200);
    }



    public function getAllVoteStats()
    {


        // Get bases with their circle information
        // Get all circles with their bases
        $circles = circle::with(['bases' => function ($query) {
            $query->select(['id', 'base_name', 'circle_id']);
        }])->get(['id', 'circle_name']);

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

        // Prepare the result array
        $result = [];
        $grandTotals = ['all' => 0, 'voted' => 0, 'not_voted' => 0];

        foreach ($circles as $circle) {
            $circleTotals = ['all' => 0, 'voted' => 0, 'not_voted' => 0];
            $basesData = [];

            foreach ($circle->bases as $base) {
                $counts = $voteCounts->get($base->id, (object) [
                    'total' => 0,
                    'voted' => 0,
                    'not_voted' => 0
                ]);

                // Convert counts to integers
                $all = (int) $counts->total;
                $voted = (int) $counts->voted;
                $not_voted = (int) $counts->not_voted;

                $basesData[$base->base_name] = [
                    'base_id' => $base->id,
                    'base_name' => $base->base_name,
                    'all' => $all,
                    'voted' => (string) $voted, // Convert to string as in your example
                    'not_voted' => (string) $not_voted // Convert to string as in your example
                ];

                // Update circle totals
                $circleTotals['all'] += $all;
                $circleTotals['voted'] += $voted;
                $circleTotals['not_voted'] += $not_voted;

                // Update grand totals
                $grandTotals['all'] += $all;
                $grandTotals['voted'] += $voted;
                $grandTotals['not_voted'] += $not_voted;
            }

            $result[] = [
                'circle_id' => $circle->id,
                'circle_name' => $circle->circle_name,
                'bases' => $basesData,
                'circle_totals' => [
                    'all' => $circleTotals['all'],
                    'voted' => (string) $circleTotals['voted'], // Convert to string
                    'not_voted' => (string) $circleTotals['not_voted'] // Convert to string
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'total' => $grandTotals['all'],
            'total_vote' => (string) $grandTotals['voted'], // Convert to string
            'total_note_vote' => (string) $grandTotals['not_voted'] // Convert to string
        ], 200);
        //
    }



    // public function getAllNotVote()
    // {

    //     // Get paginated non-voting employees grouped by base
    //     $notVotedByBase = Base::with(['cirlce', 'employeeVotes' => function ($query) {
    //         $query->where('is_election', false)
    //             ->select('id', 'fullName', 'mobile', 'base_id', 'is_election');
    //     }])
    //         ->select('id', 'base_name')
    //         ->orderBy('base_name')
    //         ->paginate(100);

    //     // Transform the data structure
    //     $formattedData = $notVotedByBase->map(function ($base) {
    //         return [
    //             // 'base_id' => $base->id,
    //             'base_name' => $base->base_name,
    //             //'circle_name' => $cirlce->cirlce_name,
    //             //   'not_voted_count' => $base->employeeVotes->count(),
    //             'list' => $base->employeeVotes->map(function ($employee) {
    //                 return [
    //                     'id' => $employee->id,
    //                     'fullName' => $employee->fullName,
    //                     'mobile' => $employee->mobile,
    //                     'unit_office' => $employee->unit_office,
    //                     'note' => $employee->note
    //                 ];
    //             })
    //         ];
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'data' =>  $formattedData,
    //         'pagination' => [
    //             'total' => $notVotedByBase->total(),
    //             'per_page' => $notVotedByBase->perPage(),
    //             'current_page' => $notVotedByBase->currentPage(),
    //             'last_page' => $notVotedByBase->lastPage()

    //         ]
    //     ], 200);
    // }

    public function getAllNotVote()
    {
        // Eager load only the needed columns from employee_votes
        $notVotedByBase = Base::with(['employeeVotes' => function ($query) {
            $query->where('is_election', false)
                ->select('id', 'fullName', 'mobile', 'unit_office', 'note', 'base_id');
        }])
            ->select('id', 'base_name')
            ->orderBy('base_name')
            ->paginate(100);

        // Transform data efficiently
        $formattedData = $notVotedByBase->map(function ($base) {
            return [
                'base_name' => $base->base_name,
                'list' => $base->employeeVotes->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'fullName' => $employee->fullName,
                        'mobile' => $employee->mobile,
                        'unit_office' => $employee->unit_office,
                        'note' => $employee->note,
                    ];
                })->values(), // Reset keys for cleaner JSON
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'pagination' => [
                'total' => $notVotedByBase->total(),
                'per_page' => $notVotedByBase->perPage(),
                'current_page' => $notVotedByBase->currentPage(),
                'last_page' => $notVotedByBase->lastPage(),
            ]
        ], 200);
    }



    // amar employee
    public function amar()
    {
        $allEmployee = EmployeeVote::count();
        $allEmployeeNotVote = EmployeeVote::where('is_election', '=', false)->count();
        $allEmployeeVote = EmployeeVote::where('is_election', '=', true)->count();

        return response()->json([
            'success' => true,
            'allEmployee' => $allEmployee,
            'allEmployeeNotVote' => $allEmployeeNotVote,
            'allEmployeeVote' => $allEmployeeVote,
        ]);
    }
}
