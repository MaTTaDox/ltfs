<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class AwsAsController extends BaseController
{
    /**
     * @Rest\Route("api/aws_as_sns", name="aws_as_sns", methods={"POST"})
     */
    public function handleSNS(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $host = $request->getHost();
        if (strpos($host, '.amazonaws.com') === false) {
            return $this->renderRest([]);
        }
        
        $type = $content['Type'];

        if (method_exists($this, $type . 'Action')) {
            $this->{$type . 'Action'}($content);
        }

        return $this->renderRest([]);
    }

    public function SubscriptionConfirmationAction($content)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $content['SubscribeURL'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => []
        ]);

        curl_close($curl);
    }
}
