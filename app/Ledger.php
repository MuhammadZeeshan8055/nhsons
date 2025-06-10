<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $table = 'ledgers';

    protected $fillable = ['customer_id','bill_number','bill_amount','amount_paid','transaction_date'];

    protected $hidden = ['created_at','updated_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
