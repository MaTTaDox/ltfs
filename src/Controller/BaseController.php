<?php


namespace App\Controller;

use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BaseController extends AbstractController
{
    use ControllerTrait;

    /**
     * BaseController constructor.
     * @param ViewHandlerInterface $handler
     */
    public function __construct(ViewHandlerInterface $handler)
    {
        $this->setViewHandler($handler);
    }

    protected function renderRest($response, $code = 200, $headers = [])
    {
        $view = View::create($response, $code, $headers);

        return $this->handleView($view);
    }
}