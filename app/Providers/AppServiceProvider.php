<?php

namespace App\Providers;

use App\Http\Controllers\TwilioController;
use App\Mail\VerifyUser;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema:: defaultStringLength(191);
        User::created(function($user){
            /*
           retry(5, function()use ($user) {
                Mail::to($user->email)->send(new VerifyUser($user));
           }, 100);
           */

         //  $sendVerification= new TwilioController();
         //  $sendVerification->sendCode('+234'.$user->phone);

        });
        //
    }
}
