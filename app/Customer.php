<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['nama', 'alamat', 'email', 'telepon'];

    protected $hidden = ['created_at', 'updated_at'];

    // One-to-many relationship
    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
}
