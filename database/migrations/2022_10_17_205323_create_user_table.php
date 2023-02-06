<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('company_id');
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('contact_number')->unique()->nullable();
            $table->string('email')->unique();
            $table->integer('gst_no')->nullable();
            $table->integer('otp')->nullable();
            $table->timestamp('otp_send_at')->nullable();
            $table->integer('otp_verified_status')->default(0)->comment('0:not Verified,1:verified');
            $table->timestamp('otp_verified_at')->nullable();
            $table->integer('status')->default(0)->comment('0:pending,1:approved,2:blocked');
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('is_admin')->default(0)->comment('0:not admin,1:admin');
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
        Schema::dropIfExists('user');
    }
};
