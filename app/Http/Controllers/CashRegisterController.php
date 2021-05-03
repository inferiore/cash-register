<?php

namespace App\Http\Controllers;

use App\CashRegisterBalance;
use App\Events\ChargeCashRegisterEvent;
use App\Http\Requests\AddPaymentRequest;
use App\Http\Requests\ChargeCashRegisterRequest;
use App\Support\Types\TransactionTypes;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
class CashRegisterController extends Controller
{
    protected $cashRegisterBalance ;

    public  function __construct(CashRegisterBalance  $cashRegisterBalance){
        $this->cashRegisterBalance = $cashRegisterBalance;
    }

    public function addPayment(AddPaymentRequest $request){
        $cashReceived = collect($request->get("payment"));
        $cashReceived = $cashReceived->transform(function($item){
            $item["amount"] = $item["quantity"] * $item["value"];
            return $item;
        });
        $amountReceived = $cashReceived->sum("amount");
        $change =   $amountReceived - $request->get("amountToPay");
        if($change < 0){
            return response()->json(["message"=>"Amount to paid is less than was received."]);
        }
        $changeDenominations = $this->cashRegisterBalance->change($change);
//        $cashReceived = $cashReceived->each(function($item){
//            $item["denomination"] = $item["value"];
//        });
        $changeDenominations = $changeDenominations->transform(function($item){
            $item["value"] = $item["denomination"];
            return $item;
        });

        DB::beginTransaction();
        try {
            $transaction = Transaction::create(["amount" => $amountReceived,"transaction_type" => TransactionTypes::Accredit]);
            $transaction->details()->createMany($cashReceived);
            ChargeCashRegisterEvent::dispatch($transaction,$cashReceived);

            $change = Transaction::create(["amount" => -$change,"transaction_type" => TransactionTypes::Deduct]);
            $change->details()->createMany($changeDenominations);
            ChargeCashRegisterEvent::dispatch($change,$changeDenominations);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error at addPayment function in CashRegisterController: Exception".json_encode($e->getMessage()));
            throw new $e;

        }
        return response()->json(["change" => $changeDenominations]);
    }

    public function charge(ChargeCashRegisterRequest $request){

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
            Log::error("Error at charge function in CashRegisterController: Exception".json_encode($e->getMessage()));
            throw new \Exception("Error when saving data");

        }


        return response()->json(["amount_received",$amount]);
    }

    public function empty(AddPaymentRequest $request){

        $transaction_details = collect(CashRegisterBalance::all()->toArray());
        $transaction_details = $transaction_details->transform(function($item){
            $item["amount"] = $item["quantity"] * $item["denomination"]*-1;
            $item["value"] = $item["denomination"] *-1;
            unset ($item["denomination"]);
            return $item;
        });
        $amount = $transaction_details->sum("amount");

        DB::beginTransaction();
        try {
            $transaction = Transaction::create(["amount" => $amount,"transaction_type" => TransactionTypes::Deduct]);
            $transaction->details()->createMany($transaction_details);
            ChargeCashRegisterEvent::dispatch($transaction,$transaction_details);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error at empty function in CashRegisterController: Exception".json_encode($e->getMessage()));
            throw $e;
        }
        return response()->json(["amount_returned",$amount]);
    }

    public function status(){

        $status = collect(CashRegisterBalance::select("denomination","quantity","amount")->get()->toArray());

        return response()->json(["status"=>$status,"cash"=>$status->sum("amount")]);
    }

    public function logs(){
        $transactions = Transaction::with("details")->get();
        return response()->json(["logs"=>$transactions]);
    }



}
