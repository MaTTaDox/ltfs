<?php


namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $code = 500;
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        }


        $message = $exception->getMessage();

        if (!strlen($message)) {
            $message = Response::$statusTexts[$code];
        }

        $responseData = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ];

        if (getenv('APP_ENV') == 'dev') {
            $responseData['error']['trace'] = $exception->getTrace();
        }

        $event->setResponse(new JsonResponse($responseData, $code));
    }
}