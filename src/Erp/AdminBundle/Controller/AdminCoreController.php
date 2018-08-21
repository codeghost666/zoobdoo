<?php

namespace Erp\AdminBundle\Controller;

use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CoreController as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminCoreController extends BaseController
{
    /**
     * @param $apiSecretKey
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function generatePaySimpleServerTokenAction($apiSecretKey)
    {
        $user = $this->getUser();

        if (!$user->hasRole(User::ROLE_SUPER_ADMIN)) {
            throw new AccessDeniedException;
        }

        $apiSecretKeyConfig = $this->getParameter('paysimple.api_secret_code');
        if ($apiSecretKey !== $apiSecretKeyConfig) {
            throw new AccessDeniedException;
        }
        $token = $this->getServerToken();

        return new Response($token);
    }

    /**
     * Generate server token via PaySimple API
     *
     * @return mixed
     */
    private function getServerToken()
    {
        $apiUserName = $this->getParameter('paysimple.user_name');
        $apiSecretKey = $this->getParameter('paysimple.api_secret_code');
        $url = "https://sandbox-api.paysimple.com/v4/customer";

        $timestamp = gmdate("c");
        $hmac = hash_hmac("sha256", $timestamp, $apiSecretKey, true); //note the raw output parameter
        $hmac = base64_encode($hmac);
        $auth = "Authorization: PSSERVER AccessId = $apiUserName; Timestamp = $timestamp; Signature = $hmac";
        /** @var $curl \Erp\CoreBundle\Services\Curl */
        $curl = $this->get('erp.curl');
        $responseCode = $curl->setHeaders([$auth])->execute($url, true);

        return $responseCode;
    }
}
