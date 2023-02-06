<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';
    protected $fillable = ['company_id', 'customer_id','description','price', 'discount', 'total' ];  

    

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'invoice_id');
    }

    public function customers()
    {
        return $this->hasOne(Customer::class, 'customer_id', 'customer_id');
    }
}
