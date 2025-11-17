<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers(){
        $users = User::all();

        return response()->json(["message"=>"all users","data:"=>$users],200);
    }

    public function createUser(Request $request){
        $request->validate([
            'email'=>'required|email|unique:users',
            'password'=>'required'
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json(['message' => 'email already exists', 'error' => ''], 400);
        }

        $user = new User();
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json(["message"=>'user created successfully', 'data' => $user],201);
    }

}
