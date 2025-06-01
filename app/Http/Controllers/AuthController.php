<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['login', 'register']);
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if(!$token = Auth::attempt($credentials)){
            return response()->json(['message'=>'Unauthorized. Credentials do not match'], 401);
        }
        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
            'expires_in' => config('jwt.ttl') * 60, // JWT TTL in seconds
        ], 200);
    }

    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id', 
        ]);

        try{
           $user = (User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id' => $data['role_id'],
            ]));
        }catch (\Exception $e) {
            return response()->json([$e], 500);
        }
        return response()->json([   
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function refresh()
    {
        try{
            $token = Auth::refresh();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token refresh failed. Please login again.'], 401);
        }
        return response()->json([
            'status'=> 'success',
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function me(){
        return response()->json(Auth::user(), 200);
    }
}
