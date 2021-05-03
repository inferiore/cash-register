<?php

namespace App\Repository;

interface CashRegisterRepositoryInterface
{
    /**
     * @return mixed
     */
    public function logs();

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function statusWithDate($startDate, $endDate);
}
