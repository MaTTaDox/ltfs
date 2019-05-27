<?php

namespace App\Controller;

use App\Facades\LogFacade;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;

class AwsAsController extends BaseController
{
    /**
     * @Rest\Route("api/aws_as_sns", name="aws_as_sns", methods={"POST"})
     */
    public function handleSNS(Request $request)
    {
        /** @var Message $message */
        $message = Message::fromRawPostData();

        $validator = new MessageValidator();
        if (!$validator->isValid($message)) {
            return $this->renderRest([]);
        }


        $type = $message['Type'];

        if (method_exists($this, $type . 'Action')) {
            $this->{$type . 'Action'}($message->toArray());
        }

        LogFacade::log('WARNING', 'Action not found: ' . $type . 'Action');

        return $this->renderRest([]);
    }

    public function SubscriptionConfirmationAction($content)
    {
        file_get_contents($content['SubscribeURL']);
    }

    public function NotificationAction($content)
    {
        LogFacade::log('WARNING', 'SNS Notification', $content);
    }
}
