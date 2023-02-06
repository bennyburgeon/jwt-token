<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'invoice_details';
    protected $primaryKey = 'detail_id';
    protected $fillable = ['invoice_id', 'product_id','price', 'cgst', 'sgst' ];  

    public function products()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }
}
