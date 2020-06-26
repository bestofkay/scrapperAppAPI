<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('password');
            $table->string('country');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('activation_code')->nullable();
            $table->string('account_verified')->default(User::UNVERIFIED_USER);
            $table->string('last_login')->nullable();
            $table->string('status')->default(User::UNVERIFIED_USER);;
            $table->string('is_admin')->default(User::NOT_ADMIN);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
