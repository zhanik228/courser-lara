<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function register(Request $request) {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();
            $messages = $errors->messages();
            $violations = [];

            foreach($messages as $field => $message) {
                $violations[$field] = [
                    'message' => $message
                ];
            }

            return response()->invalid($violations);
        }

        $newUser = new User();
        $newUser->name = $request->name;
        $newUser->last_name = $request->last_name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        $newUser->save();

        $user_role = new UserRoles();
        $user_role->user_id = $newUser->id;
        $user_role->role_id = 2;
        $user_role->save();

        $token = $newUser->createToken('token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function login(Request $request) {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();
            $messages = $errors->messages();
            $violations = [];

            foreach($messages as $field => $message) {
                $violations[$field] = [
                    'message' => $message[0]
                ];
            }

            return response()->invalid($violations);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong username or password'
            ]);
        }

        $token = $user->createToken('token')->plainTextToken;
        $role = User::where('email', $request->email)->first()->role;

        return response()->json([
            'status' => 'success',
            'token' => $token,
        ]);
    }

    public function getUserByToken(Request $request) {
        return PersonalAccessToken::findToken($request->token)->tokenable;
    }

    public function setRole(Request $request) {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'role' => 'required'
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();
            $messages = $errors->messages();
            $violations = [];

            foreach ($messages as $field => $message) {
                $violations[$field] = [
                    'message' => $message[0]
                ];
            }
        }

        $userId = User::where('email', $request->email)->first()->id;
        $roleId = Role::where('name', $request->role)->first()->id;

        return UserRoles::where('user_id', $userId)->update(['role_id' => $roleId]);

    }
}
