<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
// use Stevebauman\Location\Facades\Location;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'bnka' => 'required',
            'role' => 'required',
            'circle_id' => 'required',
            'phone' => 'required|min:11',
            'isActive' => 'required',
        ]);


        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->base_id = $request->bnka;
        $user->role = $request->role;
        $user->phone = $request->phone;
        $user->isActive = $request->isActive;
        $user->circle_id = $request->circle_id;
        //circle_id

        $user->save();

        return response()->json([
            'success' => true,
            'user' => $user,
            // 'token' => $user->createToken('auth_token')->plainTextToken
            'message' => 'register user successfully'
        ], 201);
    }

    public function login(Request $request)
    {


        try {



            // return response()->json([
            //     'data' => $request->all,
            // ]);

            // ✅ Validation
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // ✅ Fetch user with relations (only needed fields)
            $user = User::with(['circle:id,circle_name', 'base:id,base_name'])
                ->where('email', $credentials['email'])
                // ->select(['id', 'name', 'role', 'isActive', 'email', 'password', 'circel_name', 'base_name'])
                ->first();

            // ❌ If no user or password mismatch
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ئیمەیڵ یان ووشەی نهێنی هەڵەیە'
                ], 201);
            }

            // ❌ If account inactive
            if ($user->isActive == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ئەم هەژمارە ناچالاکە'
                ], 403);
            }



            // Get client IP and user agent


            // return  response()->json([
            //     'ip' => $request->all(),
            // ]);
            // // You can override IP for testing:
            // // $ip = '66.102.0.0'; // Google's public IP

            // // $ip = ; // Replace with request IP if needed
            //$ip = $request->ip; //'10.77.142.29'; // e.g., 192.168.1.1 or real IP

            // // Get location data
            // $location = Location::get($ip);
            // // if (!$location) {
            // //     return response()->json(['error' => 'Location not found'], 404);
            // // }

            // return response()->json([
            //     'ip' => $ip,
            //     'country' => $location->countryName,
            //     'countryCode' => $location->countryCode,
            //     'region' => $location->regionName,
            //     'city' => $location->cityName,
            //     'latitude' => $location->latitude,
            //     'longitude' => $location->longitude,
            //     'timezone' => $location->timezone,
            // ]);
            // Log login data
            $ip = $request->ip;

            LoginLog::create([
                'user_id' => $user->id,
                'ip_address' => $ip,
                'status' => 'login'
            ]);

            // ✅ Success → Create Token
            return response()->json([
                'success' => true,
                'user'    => $user->makeHidden('password'), // 🔒 Hide password
                'token'   => $user->createToken('auth_token')->plainTextToken
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => $error->getMessage(), // Fixed syntax error
            ], 500); // Added proper HTTP status code
        }
    }

    public function logout(Request $request)
    {


        // Get client IP and user agent
        $ip = $request->ip;
        // Log login data
        LoginLog::create([
            'user_id' => Auth::user()->id,
            'ip_address' => $ip,
            'status' => 'logout'
        ]);
        $request->user()->currentAccessToken()->delete();


        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function getUserList()
    {
        if (Auth::user()->role == 'admin') {
            $user = User::where('circle_id', Auth::user()->circle_id)
                ->where('role', '!=', 'superadmin')
                ->with(['circle', 'base'])
                ->orderby('id', 'desc')
                ->get();
        }
        if (Auth::user()->role == 'superadmin') {
            $user = User::with(['circle', 'base'])
                ->orderby('id', 'desc')
                ->get();
        }
        return response()->json([
            'success' => true,
            'message' => 'all user',
            'data' => $user,
        ]);
    }
    public function getUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'user not found',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'user',
            'data' => $user,
        ]);
    }
    public function userUpdate(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $id,
            'bnka' => 'required',
            'role' => 'required',
            'circle_id' => 'required',
            'phone' => 'required|min:11',
            'isActive' => 'required',
        ]);


        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->base_id = $request->bnka;
        $user->role = $request->role;
        $user->phone = $request->phone;
        $user->isActive = $request->isActive;
        $user->circle_id = $request->circle_id;

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'update user successfully'
        ], 201);
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        $user = user::find($id);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }


    public function index(Request $request)
    {
        $query = LoginLog::query();

        if ($request->has('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json($logs);
    }
}
