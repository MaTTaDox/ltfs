<?php

namespace App\Controller;

use App\Entity\User;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Google_Client;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginController extends BaseController
{
    /**
     * @Rest\Route("api/google_login", name="login")
     */
    public function login(
        Request $request,
        AuthenticationFailureHandler $failureHandler,
        AuthenticationSuccessHandler $successHandler,
        JWTTokenManagerInterface $manager
    )
    {
        try {
            $client = new Google_Client(['client_id' => getenv('GOOGLE_CLIENT_ID')]);  // Specify the CLIENT_ID of the app that accesses the backend
            $payload = $client->verifyIdToken($request->get('id_token'));
            if ($payload) {

                $userId = $payload['sub'];

                $email = $payload['email'];

                $userRepo = $this->getDoctrine()->getRepository(User::class);

                $jwtUser = $userRepo->find($userId);
                if (!$jwtUser) {
                    $jwtUser = new User();
                    $jwtUser->setUsername($userId);
                }

                $settings = $jwtUser->getSettings();

                $settings['email'] = $email;

                $jwtUser->setSettings($settings);

                $em = $this->getDoctrine()->getManager();
                $em->persist($jwtUser);
                $em->flush();

                return $successHandler->handleAuthenticationSuccess($jwtUser);
            }

        } catch (Exception $e) {
        }

        return $failureHandler->onAuthenticationFailure($request, new AuthenticationException);
    }
}
