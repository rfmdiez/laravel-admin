<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){

        //Postman Headers X-Requested-With  XMLHttpRequest
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
        ]);

        return response(new UserResource($user),Response::HTTP_CREATED);
    }

    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response([
                'error' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $jwt = $user->createToken('token')->plainTextToken;

        //Change supports_credentials to true in config/cors.php
        //Check app/Http/Middleware/Authenticate.php
        $cookie = cookie('jwt',$jwt,60*24);


        return response([
            'jwt' => $jwt
        ])->withCookie($cookie);
    }

    public function user(Request $request){
        $user = $request->user();
        return new UserResource($user->load('role'));
    }

    public function logout(){
        $cookie = \Cookie::forget('jwt');
        Auth::user()->tokens()->delete();

        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }
}
