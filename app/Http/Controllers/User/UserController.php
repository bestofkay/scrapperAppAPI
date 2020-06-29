<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TwilioController;
use App\Mail\ForgetPassword;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends BaseController
{

    public function __construct()
    {
        //$this->middleware('client_credentials')->only(['store']);
        $this->middleware('auth:api')->except(['register','verify','index','forget_password','verify_phone','destroy']);
       // $this->middleware('auth:api');
    }
    public function index()
    {
        //
        $foo = new User();
        $clients=User::all();
        return $this->showAll($clients);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $rules=[
            'first_name'=>'required',
            'last_name'=> 'required',
            'email' => 'unique:users|required|email',
            'phone'=>'unique:users|required',
            'password'=> 'required|min:6',
        ];
        //dd($request); exit;
        $foo = new User();
        $request->validate($rules);
        $data = $request->all();
        $data['password']= bcrypt($request->password);
        $data['account_verified'] = User::UNVERIFIED_USER;
        $data['status'] = User::INACTIVE;
        $data['activation_code'] = $foo->generateActivationCode();
        $data['is_admin'] = User::IS_ADMIN;
        $data['country'] ='Nigeria';

        $client = User::create($data);
/*
        $success['token'] =  $client->createToken('myToken')->accessToken;
        $success['user_id'] =  $client->id;
        $success['expiration'] =  strtotime(Carbon::now()->addDays(1));
*/
        return $this->saves($client);
    }

    public function register(Request $request)
    {
        /*
        $rules=[
            'first_name'=>'required',
            'last_name'=> 'required',
            'email' => 'unique:users|required|email',
            'phone'=>'unique:users|required',
            'password'=> 'required|min:6',
        ];
        */
        //dd($request); exit;
        $foo = new User();
       // $request->validate($rules);
       $user= User::where('email', $request->email);
       $phone= User::where('phone', $request->phone);
       if(empty($user) && empty($phone)){
        $data = $request->all();
        $data['password']= bcrypt($request->password);
        $data['phone_code']= '+234';
        $data['account_verified'] = User::UNVERIFIED_USER;
        $data['status'] = User::INACTIVE;
        $data['activation_code'] = $this->getName(6);
        $data['is_admin'] = User::IS_ADMIN;
        $data['country'] ='Nigeria';

        $client = User::create($data);
        $data=array(
            'user'=>$client,
            'status'=>true,
        );
        return $this->saves($data);
       }else{
        return $this->errorResponse('User already exist!', 401);
       }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $clients=User::findOrFail($id);
        return $this->showOne($clients);
    }

    public function verify($token)
    {
        $user= User::where('activation_code', strtoupper($token));
        if(!empty($user)){
            $user->account_verified=User::VERIFIED_USER;
            $user->activation_code=null;
            $user->email_verified_at=time();
            $user->save();
            return $this->showMessage('The account has been verified successfully');
        }else{
            return $this->errorResponse('Wrong code entered!', 401);
        }

    }

    public function verify_phone(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|string',
            'phone' => 'required|string'
        ]);

        $sendVerification= new TwilioController();
        $verify=$sendVerification->verifyCode($request->code, $request->phone);
        if($verify){
            $user= User::where('phone', $request->phone)->firstOrfail();
            $user->account_verified=User::VERIFIED_USER;
            $user->activation_code=null;
            $user->phone_verified_at=time();
            $user->save();
            return $this->showMessage('The account has been verified successfully');
        }else{
            return $this->errorResponse('invalid code', 401);
        }

    }


public function getName($sixdigit) {
        $total_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $sixdigit; $i++) {
        $index = rand(0, strlen($total_characters) - 1);
        $randomString .= $total_characters[$index];
        }
        return strtoupper($randomString);
        }

    public function destroy($id)
    {
        $client=User::findOrFail($id);
        $client->delete();
        return response()->json(['data'=>$client], 200);

    }

    public function forget_password(Request $request)
    {
        $rules=[

            'email' => 'required|email',

        ];
        $request->validate($rules);
        $user= User::where('email', $request->email)->firstOrfail();
            $password=$this->password_generate(10);;
            $user->password= bcrypt($password);
            $user->save();
            $use['first_name']=$user->first_name;
            $use['last_name']=$user->first_name;
            $use['password']=$password;
            $use['email']=$user->email;;
           // $use=json_encode($use);
           retry(5, function()use ($use) {
            Mail::to($use['email'])->send(new ForgetPassword($use));
        }, 100);
             return $this->saves('New password successfully sent to email');

    }
}
