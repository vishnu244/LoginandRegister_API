<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Illuminate\support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    public function register(request $request)
    {
        $request->validate([
            'Name'=>'required|string',
            'Email'=>'required|string|unique:user',  
            'Password'=>'required|string',
            'Age'=>'required|string',
            'City'=>'required|string',
            'State'=>'required|string',
            'ZipCode'=>'required|string',
            'MobileNumber'=>'required|string',

        ]);
        $user = new User ([
            'Name'=> $request->Name,
            'Email'=> $request->Email,
            'Password'=> hash::make($request->Password),
            'Age'=> $request->Age,
            'City'=> $request->City,
            'State'=> $request->State,
            'ZipCode'=> $request->ZipCode,
            'MobileNumber'=> $request->MobileNumber
        ]);

        $user ->save();
        return response()->json(['message' => 'User has been Registered'],200);

    }
    public function login(Request $request)
    {
        $request -> validate([
            'email' => 'required',
            'password' => 'required|string'
        ]);

        $credentials = request(['email','passworrd']);

        if(!Auth::attempt($credentials)){
            return response()->json(['message' => 'Unauthorized'],401);

        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addweeks(1);
        $token->save();

        return response()->json(['data' => [
            'user' => Auth::user(),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]]);
    }
}
