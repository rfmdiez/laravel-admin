<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function index()
    {
        $this->authorize('view','users');
        return UserResource::collection(User::with('role')->paginate());
    }

    public function store(UserCreateRequest $request)
    {
        $this->authorize('edit','users');
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
        ]);

        return response(new UserResource($user),Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $this->authorize('view','users');
        return new UserResource(User::with('role')->find($id));
        //return User::with('role')->find($id);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $this->authorize('edit','users');
        $user = User::find($id);
        $user->update($request->only('first_name','last_name','email','role_id'));
        return response(new UserResource($user),Response::HTTP_ACCEPTED);
    }

    public function destroy($id)
    {
        $user = User::destroy($id);

        return response(null,Response::HTTP_NO_CONTENT);
    }

    public function updateInfo(UpdateInfoRequest $request){
        $user = $request->user();
        $user->update($request->only('first_name','last_name','email','role_id'));
        return response(new UserResource($user),Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request){
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
        return response(new UserResource($user),Response::HTTP_ACCEPTED);
    }
}
