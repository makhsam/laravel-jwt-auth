<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Role;
use App\Notifications\UserRegistered;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Role $role)
    {
        $users = User::where('role_id', $role->id)->paginate(50);

        return response()->json(['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Role $role, Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'organization' => 'required',
            'position' => 'required',
            // 'photo' => 'nullable',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create($request->all() + ['role_id' => $role->id]);

        // Generate and save username
        $user->username = $user->generateUsername();
        $user->save();

        // Notify user via email
        $user->notify(new UserRegistered($user, $request->password));

        return response()->json(['message' => 'Account has been created']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'organization' => 'required',
            'position' => 'required',
            'password' => 'nullable|confirmed|min:6'
        ]);

        // Mass update non-empty inputs in request array (`password`, `email` can be NULL)
        $user->update(array_filter($request->all()));

        return response()->json(['message' => 'The account has been updated']);
    }
}
