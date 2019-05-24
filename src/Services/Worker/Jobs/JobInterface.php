<?php


namespace App\Services\Worker\Jobs;


interface JobInterface
{
    public static function getIdentifier();

    public function work(array $data);
}