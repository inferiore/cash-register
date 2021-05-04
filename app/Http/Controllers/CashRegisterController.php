<?php

namespace App\Http\Controllers;

use App\CashRegisterBalance;
use App\Events\ChargeCashRegisterEvent;
use App\Http\Requests\AddPaymentRequest;
use App\Http\Requests\ChargeCashRegisterRequest;
use App\Repository\CashRegisterRepository;
use App\Repository\CashRegisterRepositoryInterface;
use App\Repository\TransactionRepository;
use App\Repository\TransactionRepositoryInterface;
use App\Support\Types\TransactionTypes;
use App\Transaction;
use App\TransactionDetail;
use App\Utilities\CashRegisterOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
class CashRegisterController extends Controller
{
    private $cashRegisterBalance ;
    private $repository;
    private $transactionRepository;
    public  function __construct(CashRegisterBalance  $cashRegisterBalance, CashRegisterRepositoryInterface $repository,TransactionRepositoryInterface $transactionRepository){
        $this->cashRegisterBalance = $cashRegisterBalance;
        $this->repository = $repository;
        $this->transactionRepository = $transactionRepository;
    }

    public function addPayment(AddPaymentRequest $request){

        $cashReceived = CashRegisterOperations::prepareCashReceived($request->get("payment"));

        $amountReceived = $cashReceived->sum("amount");
        $change =   $amountReceived - $request->get("amountToPay");
        if($change < 0){
            return response()->json(["message"=>"Amount to paid is less than what was received."]);
        }
        $changeDenominations = $this->cashRegisterBalance->change($change);



        $this->transactionRepository->addPayment($amountReceived,$cashReceived,$change,$changeDenominations);

        return response()->json(["change" => $changeDenominations]);
    }

    public function charge(ChargeCashRegisterRequest $request){

        $transaction_details = CashRegisterOperations::prepareCashReceived($request->get("payment"));

        $amount = $transaction_details->sum("amount");

        $this->transactionRepository->charge($amount,$transaction_details);


        return response()->json(["amount_received",$amount]);
    }

    public function empty(){

        $transaction_details = $this->repository->getAllForEmptying();

        $amount = $transaction_details->sum("amount");

        $this->transactionRepository->empty($amount,$transaction_details);

        return response()->json(["amount_returned",$amount]);
    }

    public function status(){

        $status = collect(CashRegisterBalance::select("denomination","quantity","amount")->get()->toArray());

        return response()->json(["status"=>$status,"cash"=>$status->sum("amount")]);
    }

    public function logs(){
        $transactions = $this->repository->logs();
        return response()->json(["logs"=>$transactions]);
    }

    public function statusWithDate(request $request){
        $transactions = $this->repository->statusWithDate($request->get("start_date"),$request->get("end_date"));
        return response()->json(["logs"=>$transactions,"cash"=>$transactions->sum("amount")]);
    }




}
