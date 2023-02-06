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
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('customer_id');
            $table->integer('company_id')->unsigned(); 
            $table->foreign('company_id')->references('company_id')->on('users'); 
            $table->string('customer_name')->nullable();
            $table->bigInteger('contact_number')->nullable();
            $table->string('email')->unique();
            $table->integer('status')->default(1)->comment('0:in active,1:active');
            $table->text('address')->nullable();
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
        Schema::dropIfExists('customer');
    }
};
