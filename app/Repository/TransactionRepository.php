<?php


namespace App\Repository;
use App\Events\ChargeCashRegisterEvent;
use App\Support\Types\TransactionTypes;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class TransactionRepository implements TransactionRepositoryInterface
{

    /**
     * @param $amountReceived
     * @param $cashReceived
     * @param $change
     * @param $changeDenominations
     * @return mixed
     * @throws \Exception
     */
    public function addPayment($amountReceived,$cashReceived,$change,$changeDenominations){

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
            Log::error("Error at addPayment function: Exception".json_encode($e->getMessage()));
            throw new $e;

        }
    }

    public function charge($amount,$transaction_details){

        DB::beginTransaction();
        try {
            $transaction = Transaction::create(["amount" => $amount,"transaction_type" => TransactionTypes::Accredit]);
            $transaction->details()->createMany($transaction_details);
            ChargeCashRegisterEvent::dispatch($transaction,$transaction_details);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error at charge function : Exception".json_encode($e->getMessage()));
            throw new \Exception("Error when saving data");
        }
    }

    public function empty($amount,$transaction_details){
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
    }

}
