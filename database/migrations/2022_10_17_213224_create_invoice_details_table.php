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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->increments('detail_id');
            $table->integer('invoice_id')->unsigned(); 
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices');
            $table->integer('product_id')->unsigned(); 
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->integer('quantity')->nullable();
            $table->float('price', 8, 2)->nullable();
            $table->float('cgst', 8, 2)->nullable();
            $table->float('sgst', 8, 2)->nullable();
            $table->float('total', 8, 2)->nullable();
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
        Schema::dropIfExists('invoice_details');
    }
};
