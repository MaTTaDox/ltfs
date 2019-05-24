<?php


namespace App\Services\Worker\Jobs;


use App\Facades\LogFacade;

class CreateVaultJob implements JobInterface
{
    public function work(array $data)
    {



        LogFacade::log('INFO', 'create vault');
    }

    public static function getIdentifier()
    {
        return 'createVault';
    }


}