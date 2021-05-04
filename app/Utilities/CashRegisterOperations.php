<?php


namespace App\Utilities;


class CashRegisterOperations
{

    public static function prepareCashReceived($cash){
        $cashReceived = collect($cash);
        $cashReceived = $cashReceived->transform(function($item){
            $item["amount"] = $item["quantity"] * $item["value"];
            return $item;
        });
        return $cashReceived;
    }
}
