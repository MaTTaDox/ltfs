<?php


namespace App\Services\Worker;


use App\Facades\LogFacade;
use App\Services\Worker\Jobs\CreateVaultJob;
use App\Services\Worker\Jobs\JobInterface;
use TriTran\SqsQueueBundle\Service\Message;
use TriTran\SqsQueueBundle\Service\Worker\AbstractWorker;

class JobWorker extends AbstractWorker
{
    /**
     * @var JobInterface[]
     */
    protected $jobs = [];

    public function __construct(CreateVaultJob $createVaultJob)
    {
        $this->jobs[$createVaultJob::getIdentifier()] = $createVaultJob;
    }

    protected function execute(Message $message)
    {
        $job = json_decode($message->getBody(), true);

        LogFacade::log('INFO', $message->getBody());

        $identifier = $job['identifier'];

        if (!isset($this->jobs[$identifier])) {
            return true;
        }

        $jobExecutor = $this->jobs[$identifier];

        $jobExecutor->work($job);

        return true;
    }

}