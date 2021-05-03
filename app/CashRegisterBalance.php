<?php

namespace App;

use App\Traits\Singleton;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CashRegisterBalance extends Model
{


    protected $table = "cash_register_balance";
    protected $fillable = ["denomination","quantity","amount"];








}
