<?php


namespace App\Repository;
use App\CashRegisterBalance;
use App\Transaction;
use Illuminate\Support\Facades\DB;

class CashRegisterRepository implements CashRegisterRepositoryInterface
{

    /**
     * @return mixed
     */
    public function logs(){
        return Transaction::with("details")->get();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function statusWithDate($startDate, $endDate){
        return  DB::table("transaction_details")
            ->where("created_at",">=",$startDate)
            ->where("created_at","<=",$endDate)
            ->select(DB::raw("ABS(value) denomination"),DB::raw("sum(quantity) quantity"),DB::raw("sum(amount) amount"))
            ->groupBy(DB::raw("ABS(value)"))
            ->get();

    }

    public function getAllForEmptying(){
        $transaction_details = collect(CashRegisterBalance::all()->toArray());
        $transaction_details = $transaction_details->transform(function($item){
            $item["amount"] = $item["quantity"] * $item["denomination"]*-1;
            $item["value"] = $item["denomination"] *-1;
            unset ($item["denomination"]);
            return $item;
        });
        return $transaction_details;
    }

}
