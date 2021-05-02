<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Support\Statics\AcceptableDenominations;
use Illuminate\Support\Facades\Log;
class DefaultCashRegisterBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        foreach (AcceptableDenominations::Denominations as $value){
            $data [] =["denomination" => $value,"quantity"=>0, "amount"=>0]  ;
        }
//        Log::info(var_dump($data));
        \App\CashRegisterBalance::insert($data);

    }
}
