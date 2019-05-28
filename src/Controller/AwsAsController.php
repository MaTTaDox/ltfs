<?php

namespace App\Controller;

use App\Facades\LogFacade;
use Aws\Ec2\Ec2Client;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use FOS\RestBundle\Controller\Annotations as Rest;

class AwsAsController extends BaseController
{
    /**
     * @Rest\Route("api/aws_as_sns", name="aws_as_sns", methods={"POST"})
     */
    public function handleSNS()
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
        $ec2Client = new Ec2Client([
            'version' => 'latest',
            'region' => 'eu-central-1',
            'credentials' => [
                'key' => getenv('AWS_ACCESS_KEY'),
                'secret' => getenv('AWS_SECRET_KEY')
            ],
        ]);

        $instances = $ec2Client->describeInstances([
            'Filters' => [
                [
                    'Name' => 'tag:aws:autoscaling:groupName',
                    'Values' => ['AutoScaling LTFS']
                ]
            ]
        ]);

        $ips = [];
        foreach ($instances['Reservations'] as $reservation) {
            foreach ($reservation['Instances'] as $instance) {
                $ips[] = $instance['PublicIpAddress'];
            }
        }

        $template = $this->render('nginx/lbvhost.twig', [
            'ips' => $ips
        ]);

        file_put_contents('/etc/nginx/conf.d/load-balancer.conf', $template);

        shell_exec('sudo /etc/init.d/nginx reload');

    }
}
