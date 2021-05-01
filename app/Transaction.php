<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    //

    protected $fillable = ["amount","transaction_type"];

    public function details():HasMany{
        return $this->hasMany(TransactionDetail::class);
    }
}
