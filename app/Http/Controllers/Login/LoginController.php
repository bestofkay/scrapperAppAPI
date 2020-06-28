<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class LoginController extends BaseController
{

    public function store(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $users= User::findOrFail($user->id);
           // $user=Auth::create();
            $success['token'] =  $user->createToken('myToken')->accessToken;
             $success['user'] =  $users;
             $success['expiration'] =  strtotime(Carbon::now()->addDays(1));
             $data=array(
                 'token'=>$success,
                 'data'=>'User login successfully'
             );
             return response()->json($data, 200);
            }
        else{
            return $this->errorResponse('Invalid username/password', 401);
        }

    }

}
