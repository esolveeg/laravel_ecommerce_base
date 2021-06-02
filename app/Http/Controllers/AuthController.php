<?php

namespace App\Http\Controllers;

use App\User;
use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // dd(User::all());
        $rules = [
            'password' => 'required|max:255',
            'email' => 'required|email|max:255',
        ];
        $user = User::where('email' , $request->email)->first();
        if(!$user){
            return response()->json("email_not_found",400);
        }
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        // dd($request->password);
        if (!Hash::check($request->password, $user->password)) {
            return response()->json("password_not_match",400);
        }
     
        $tokenRequest = $this->loginAction($user , $request->password);
        return app()->handle($tokenRequest);


    }
    public function register(Request $request)
    {
        // dd($request->phone);
        if(User::where('email' , $request->email)->count() > 0){
            return response()->json("email_already_exists",400);
        }
        $rules = [
        'email' => 'required|email|max:255',
        'password' => 'required|min:6|max:255',
        'name' => 'required|max:255',
        'phone' => 'required|max:255|unique:users'];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $user = [
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'name' => $request->name,
            'phone' => $request->phone,
        ];
        // dd($user);
        $user =  User::create($user);
        
        if(!$user) return   response()->json("registration_faild",500);
        return response()->json($user);
    }
    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key){
            $token->delete();
        });

        return response()->json("logged out successfully",200);

    }
    protected function loginAction($user , $passwrod){
        $passwordGrantClient = Client::find(env('PASSPORT_CLIENT_ID', 1));
        
        // dd($passwordGrantClient);
        $data = [
            'grant_type' => 'password',
            'client_id' => $passwordGrantClient->id,
            'client_secret' => $passwordGrantClient->secret,
            'username' => $user->email,
            'password' => $passwrod,
            'scope' => '*',
        ];
       
        return  Request::create('oauth/token' , 'post', $data );
    }
}
