<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\BasebaxiController;
use Illuminate\Http\Request;

class BaxiController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $bax = '';
    public function __construct()
    {
        //$this->middleware('client_credentials')->only(['store']);
        $this->middleware('auth:api')->except(['index']);
        $this->bax=new BasebaxiController();
       // $this->middleware('auth:api');
    }
    #########################################################################
    ################# LIST ALL WEBINARS #####################################
    public function index()
    {

    $userId=$this->UserID;
    //$meetingId='76798474823';
   $response = 'https://payments.baxipay.com.ng/api/baxipay/superagent/account/balance';
    if ($response === false) {
       // return $this->errorResponse($this->zoom->requestErrors(), 401);
       } else {
        return $response;
    }

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
