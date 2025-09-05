<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'bnka' => 'required',
            'role' => 'required',
        ]);
        // return response()->json([
        //     'user' => $request->all()
        // ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }
        // ], 201);
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->bnka = $request->bnka;
        $user->role = $request->role;

        $user->save();

        return response()->json([
            'user' => $user,
            // 'token' => $user->createToken('auth_token')->plainTextToken
            'message' => 'register user successfully'
        ], 201);
    }

    public function login(Request $request)
    {


        // return response()->json([
        //     "data" => $request->all()
        // ]);
        // return response()->json(['data' => $request->all()]);
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.'
            ]);
        } else {
            return response()->json([
                'success' => true,

                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ]);
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
        $user = User::orderby('id', 'desc')->get();

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
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $id,
            'bnka' => 'required',
            'role' => 'required',
            'isActive' => 'required',
        ]);
        // return response()->json([
        //     'user' => $request->all()
        // ]);
        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }
        // ], 201);
        $user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->bnka = $request->bnka;
        $user->role = $request->role;
        $user->isActive = $request->isActive;

        $user->save();

        return response()->json([
            'user' => $user,
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
