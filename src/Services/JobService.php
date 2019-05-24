<?php


namespace App\Services;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TriTran\SqsQueueBundle\Service\BaseQueue;
use TriTran\SqsQueueBundle\Service\Message;

class JobService extends AbstractController
{
    /**
     * @var BaseQueue
     */
    protected $queue;

    public function __construct(BaseQueue $queue)
    {
        $this->queue = $queue;
    }

    public function send($job, array $args)
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = [
            'user' => $user->getUsername(),
            'identifier' => $job,
            'arguments' => $args
        ];

        $this->queue
            ->sendMessage((new Message())->setBody(json_encode($data)));
    }
}