<?php

namespace App;

use App\Traits\Singleton;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CashRegisterBalance extends Model
{
//    use Singleton;
    protected static $instance = null;

    protected $table = "cash_register_balance";
    protected $fillable = ["denomination","quantity","amount"];


    public function accredit($collect){
        foreach ($collect as $value){
            $currentDenomination = $this->getDenomination($value["value"]);
            $data = [
                "denomination" => ( $value["value"] )
                ,"quantity" => ( $currentDenomination->quantity + $value["quantity"] )
                ,"amount" => ( $currentDenomination->amount + $value["amount"] )
            ];
            $this->updateDenomination($data);
        }
    }

    public function deduct($collect){
        foreach ($collect as $value){
            $currentDenomination = $this->getDenomination($value["value"]);
            $data = [
                "denomination" =>(  $value["value"] )
                ,"quantity" =>( $currentDenomination->quantity - $value["quantity"] )
                ,"amount" =>( $currentDenomination->amount - $value["amount"] )
            ];
            $this->updateDenomination($data);
        }
    }


    protected function updateDenomination(array $register){
        $this->where("denomination",$register["denomination"])->update(["quantity"=>$register["quantity"],"amount"=>$register["amount"]]);
    }

    protected  function getDenomination($denomination){
        return $this->where("denomination",$denomination)->first();
    }
}
