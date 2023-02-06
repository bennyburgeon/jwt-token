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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->integer('company_id')->unsigned(); 
            $table->foreign('company_id')->references('company_id')->on('users'); 
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();
            $table->text('description')->nullable();
            $table->float('price', 8, 2)->nullable();
            $table->float('cgst', 8, 2)->nullable();
            $table->float('sgst', 8, 2)->nullable();
            $table->integer('status')->default(1)->comment('1:approved,2:blocked');
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
        Schema::dropIfExists('product');
    }
};
