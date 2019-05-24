<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Clients\GlacierClient;
use App\Services\JobService;
use App\Services\Worker\Jobs\CreateVaultJob;
use Aws\Glacier\Exception\GlacierException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends BaseController
{
    /**
     * @var JobService
     */
    protected $jobService;

    public function __construct(ViewHandlerInterface $handler, JobService $jobService)
    {
        parent::__construct($handler);
        $this->jobService = $jobService;
    }

    /**
     * @Rest\Route("api/user", name="get_user", methods={"GET"})
     */
    public function getUserAction()
    {
        $user = $this->getUser();
        return $this->renderRest($user);
    }

    /**
     * @Rest\Route("api/user", name="update_user", methods={"PATCH"})
     */
    public function updateUserAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();

        $settings = $user->getSettings();

        if (isset($content['settings'])) {
            foreach (['secretKey', 'accessKey'] as $key) {
                if (!isset($content['settings'][$key])) {
                    continue;
                }
                $settings[$key] = $content['settings'][$key];
            }

            $this->validateAwsData($settings['accessKey'], $settings['secretKey']);

            $user->setSettings($settings);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->jobService->send(CreateVaultJob::getIdentifier(), []);
        }

        return $this->renderRest($user);
    }

    protected function validateAwsData($accessKey, $secretKey)
    {
        $glacier = new GlacierClient([
            'version' => 'latest',
            'region' => 'eu-central-1',
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey
            ],
        ]);

        try {
            $glacier->listVaults();
        } catch (GlacierException $e) {
            throw new BadRequestHttpException('Invalid AWS Credentials');
        }
    }
}
