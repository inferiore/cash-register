<?php

namespace App;

use App\Traits\Singleton;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CashRegisterBalance extends Model
{


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
                ,"amount" =>( $currentDenomination->amount - abs($value["amount"]) )
            ];
            $this->updateDenomination($data);
        }
    }


    protected function updateDenomination(array $register){
        $this->where("denomination",abs($register["denomination"]))->update(["quantity"=>$register["quantity"],"amount"=>$register["amount"]]);
    }

    protected  function getDenomination($denomination){
        return $this->where("denomination",abs($denomination))->first();
    }
    public function change($change){
        $cash = $this->select("denomination","quantity")->get();
        $denominationChange = collect();
        while($change != 0){
            $closestDenomination = $this->getClosestDenomination($change,$cash);
            $this->addDenominationChange($denominationChange,$closestDenomination);
            $cash->transform(function($item) use ($closestDenomination){
                if($item->denomination== $closestDenomination["denomination"]){
                     $item->quantity--;
                }
                return $item;
            });
            $change = $change - $closestDenomination["denomination"];
        }
        $denominationChange = $denominationChange->transform(function($item){
            $item["value"] = $item["denomination"];
            return $item;
        });
        return $denominationChange;


    }
    private function getClosestDenomination($change,$cash){
        $closestDenomination = $cash->where("denomination","<=",$change)->where("quantity",">",0)->sortByDesc("denomination")->first();
        if(is_null($closestDenomination)){
            throw new \Exception("There is no cash to give change.");
        }
       return  $closestDenomination->toArray();
    }

    private function addDenominationChange(&$denominationChange,$closestDenomination){
      $formatClosestDenomination =  ["denomination" => -$closestDenomination["denomination"],"quantity"=> 1,"amount"=> -$closestDenomination["denomination"]];
        if($denominationChange->where("denomination",$closestDenomination["denomination"])->count()>0){
          $denominationChange->transform(function($item) use ($closestDenomination){
                if($item["denomination"] == $closestDenomination["denomination"]){
                    $item["quantity"]++;
                }
                return $item;
            });
        }else{
            $denominationChange->push($formatClosestDenomination);
        }
    }





}
