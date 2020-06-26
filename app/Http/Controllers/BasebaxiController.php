<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class BasebaxiController extends Controller
{


    private $errors;
    private $apiKey;
    private $apiSecret;
    private $baseUrl;
    private $timeout;
    private $username;

    public function __construct($options=array() ) {
        $this->apiKey = '5adea9-044a85-708016-7ae662-646d59';

        $this->apiSecret = '5xjqQ7MafFJ5XBTN';

        $this->baseUrl = 'https://payments.baxipay.com.ng/api/baxipay/';

        $this->timeout = 30;

        $this->username = 'baxi_test';

        // Store any options if they map to valid properties
        foreach ($options as $key=>$value) {
            if (property_exists($this, $key)) $this->$key = $value;
        }
    }


}
