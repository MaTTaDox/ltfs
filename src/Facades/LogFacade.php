<?php


namespace App\Facades;


use App\Services\LogService;
use App\Services\ModuleService;
use Indragunawan\FacadeBundle\AbstractFacade;

/**
 * Class ModuleFacade
 * @package App\Facades
 *
 * @method static void log($level, $message, $context = [])
 */
class LogFacade extends AbstractFacade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return LogService::class;
    }

}