<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class LoginApiController extends Controller
{
    /**
     * Sign in the user
     * @param username
     * @param password
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:6'
        ]);

        try { // Find the user by ID
            $user = User::where('username', $request->input('username'))->firstOrFail();
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Неверное логин или пароль.'], 400);
        }

        // Verify the password and generate the token
        if ( !Hash::check($request->input('password'), $user->password)) {
            // Bad request
            return response()->json(['message' => 'Неверное логин или пароль.'], 400);         
        }

        return response()->json([
            'user' => $user,
            'token' => $user->generateToken()
        ], 200);
    }
}
