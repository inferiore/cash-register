<?php

namespace App\Http\Controllers;

use App\CashRegisterBalance;
use App\Events\ChargeCashRegisterEvent;
use App\Http\Requests\AddPaymentRequest;
use App\Support\Types\TransactionTypes;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
class CashRegisterController extends Controller
{

    public function addPayment(AddPaymentRequest $request){




    }

    public function chargeCashRegister(AddPaymentRequest $request){


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
            ChargeCashRegisterEvent::dispatch($transaction,$transaction_details);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error at addPayment function in CashRegisterController: Exception".json_encode($e->getMessage()));
            throw new \Exception("Error when saving data");

        }


        return response()->json([$transaction_details,$amount]);
    }

    public function emptyCashRegister(AddPaymentRequest $request){

        $transaction_details = TransactionDetail::where("amount",">",0);
        $amount = $transaction_details->sum("amount");
        DB::beginTransaction();
        try {
            $transaction = Transaction::create(["amount" => $amount,"transaction_type" => TransactionTypes::Deduct]);
            $transaction->details()->createMany($transaction_details);
            ChargeCashRegisterEvent::dispatch($transaction,$transaction_details);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error at addPayment function in CashRegisterController: Exception".json_encode($e->getMessage()));
            throw new \Exception("Error when saving data");

        }


        return response()->json([$transaction_details,$amount]);
    }

}
