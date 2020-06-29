<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    //
    const VERIFIED_USER= '1';
    const UNVERIFIED_USER= '0';
    const IS_ADMIN = 'true';
    const NOT_ADMIN = 'false';
    const ACTIVE='1';
    const INACTIVE='0';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'activation_code', 'account_verified','country',
        'last_login','status','is_admin',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isVerified(){
       return $this->account_verified== User::VERIFIED_USER;
    }

    public function isAdmin(){
        return $this->is_admin== User::IS_ADMIN;
    }

    public function isActive(){
        return $this->is_active== User::INACTIVE;
    }

    public function generateActivationCode(){
        return Str::random(40);
    }
}
