<?php

namespace App\Repository;

interface TransactionRepositoryInterface
{
    /**
     * @param $amountReceived
     * @param $cashReceived
     * @param $change
     * @param $changeDenominations
     * @return mixed
     * @throws \Exception
     */
    public function addPayment($amountReceived, $cashReceived, $change, $changeDenominations);

    public function charge($amount, $transaction_details);

    public function empty($amount, $transaction_details);
}
