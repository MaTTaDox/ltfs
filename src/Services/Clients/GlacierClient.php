<?php


namespace App\Services\Clients;

use App\Facades\LogFacade;
use Aws\Glacier\GlacierClient as AwsGlacierClient;
use Exception;

class GlacierClient extends AwsGlacierClient
{
    protected $client;

    public function __construct($args)
    {
        $this->client = new AwsGlacierClient($args);
    }

    /**
     * @param $name
     * @param array $args
     * @throws Exception
     */
    public function __call($name, array $args)
    {
        LogFacade::log('INFO', 'Glacier call ' . $name  , $args);
        //log
        $this->client->{$name}(...$args);
    }
}