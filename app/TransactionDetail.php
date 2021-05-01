<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{

    protected $fillable = ["amount","value","quantity","transaction_id"];

    public function transaction():BelongsTo{
        return $this->belongsTo(Transaction::class);
    }

}
