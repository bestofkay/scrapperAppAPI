<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class TwilioController extends Controller
{
  protected $sid;
  protected $authToken;
  protected $twilioFrom;
  protected $twilio_verify_sid;

  public function __construct() {
      // Initialize the Authy API and the Twilio
      //Authy_Api
     // $this->authy = new AuthyAuthyApi(config('app.twilio')['AUTHY_API_KEY']);
      // Twilio credentials
      $this->sid = getenv('TWILIO_ACCOUNT_SID');
      $this->authToken = getenv("TWILIO_AUTH_TOKEN");
      $this->twilioFrom = getenv('TWILIO_PHONE');
      $this->twilio_verify_sid = getenv('TWILIO_VERIFY_SID');
  }

  private function verifyPhone($phone) {
      $twilio = new Client($this->sid, $this->authToken);
      $twilio->verify->v2->services($this->twilio_verify_sid)
          ->verifications
          ->create($phone, "sms");
  }

  public function sendCode($phone){
      return $this->verifyPhone($phone);
  }

  public function verifyCode($code, $phone) {
    // Call the method responsible for checking the\ verification code sent.\
      $twilio = new Client($this->sid, $this->authToken);
      $verification = $twilio->verify->v2->services($this->twilio_verify_sid)
            ->verificationChecks
            ->create($code, array('to' => $phone));
        if ($verification->valid) {
            return true;
        }else{
            return false;
        }

  }


  public function sendSMS(Request $request) {
      $this->validate($request, [
          'phone' => 'required|string',
          'message' => 'required|string'
      ]);

$phone = $this->authy->phoneInfo( $request->phone, '1' );



       if ( 'landline' == $phone->bodyvar('type') ) {

           return redirect()->back()->withErrors( [ 'This is a landline' ] );

       }


      // Create REST API Client

       $client = new Client($this->sid, $this->authToken);

          $sms = $client->messages->create($request->phone,
              [
                  'from' => $this->twilioFrom,
                  'body' => $request->message
              ]
          );

          if($sms) {
              return redirect()->back()->with('success', 'SMS sent successfully');
          } else {
              return redirect()->back()->with('error', 'SMS failed!');
          }

  }
}
