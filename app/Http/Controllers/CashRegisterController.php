<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPaymentRequest;
use App\Support\Types\TransactionTypes;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashRegisterController extends Controller
{

    function addPayment(AddPaymentRequest $request){

        $transaction_details = collect($request->get("payment"));
        $transaction_details = $transaction_details->transform(function($item){
            $item["amount"] = $item["quantity"] * $item["value"];
            return $item;
        });
        $amount = $transaction_details->sum("amount");
        DB::beginTransaction();
        try {
            $transaction = Transaction::create(["amount" => $amount,"transaction_type" => TransactionTypes::Accredit]);
            $transaction->details()->createMany($transaction_details);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error at addPayment function in CashRegisterController: Exception".json_encode($e->getMessage()));
            throw new \Exception("Error when saving data");

        }

        return response()->json([$transaction_details,$amount]);
    }

}
