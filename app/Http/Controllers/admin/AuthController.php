<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
            //     'success' => false,
            //     'message' => 'Email or password incorrect.',
            //     'data' => $request->email
            // ], 401);

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
                    'message' => 'Email or password incorrect.'
                ], 401);
            }

            // ❌ If account inactive
            if ($user->isActive == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot log in. Account is inactive.'
                ], 403);
            }

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
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function getUserList()
    {
        $user = User::with(['circle', 'base'])->orderby('id', 'desc')->get();

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
}
