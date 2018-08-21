<?php

namespace Erp\PaymentBundle\PaySimple\Managers;

use Erp\PaymentBundle\PaySimple\Exceptions\PaySimpleManagerException;
use Erp\PaymentBundle\PaySimple\Exeptions\PaySimpleModelException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\CoreBundle\Services\Curl;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class PaySimpleAbstarctManager implements PaySimpleManagerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var PaySimpleModelInterface
     */
    protected $model;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $apiSecretKey;

    /**
     * @var string
     */
    protected static $apiEndpoint;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, $login = null, $apiSecretKey = null)
    {
        $this->container = $container;
        $this->curl = $this->container->get('erp.curl');
        $this->setCredentials($login, $apiSecretKey)->setApiEndpointUrl()->generateServerToken();
    }

    /**
     * @param PaySimpleModelInterface $model
     *
     * @return $this
     */
    public function setModel(PaySimpleModelInterface $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return PaySimpleManagerException
     * @throws PaySimpleModelException
     */
    public function proccess($type = '')
    {
        if (!$this->model) {
            throw new PaySimpleModelException(
                'Please, use setModel() method in child class of PaySimpleAbstractManager to setup your model'
            );
        }

        return new PaySimpleManagerException();
    }

    /**
     * Processing API response
     *
     * @param array $response
     *
     * @return array
     */
    protected function proccessResponce(array $response)
    {
        $body = json_decode($response['body'], true);

        $result = [
            'status' => PaySimpleManagerInterface::STATUS_OK,
            'data' => $body['Response'],
            'code' => $response['code'],
        ];

        if (!empty($body['Meta']['Errors'])) {
            $result = [
                'status' => PaySimpleManagerInterface::STATUS_ERROR,
                'errors' => $body['Meta']['Errors']['ErrorMessages']
            ];
        }

        return $result;
    }

    /**
     * Get success responce codes
     *
     * @return array
     */
    protected function getSuccessResponseCodes()
    {
        return [Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT];
    }

    /**
     * Get error responce codes
     *
     * @return array
     */
    protected function getErrorResponseCodes()
    {
        return [
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN,
            Response::HTTP_NOT_FOUND,
            Response::HTTP_METHOD_NOT_ALLOWED,
            Response::HTTP_INTERNAL_SERVER_ERROR
        ];
    }

    /**
     * @param string|null $login
     * @param string|null $apiSecretKey
     *
     * @return $this
     */
    private function setCredentials($login = null, $apiSecretKey = null)
    {
        $this->login = $login
            ? $login
            : $this->container->getParameter('paysimple.user_name');

        $this->apiSecretKey = $apiSecretKey
            ? $apiSecretKey
            : $this->container->getParameter('paysimple.api_secret_code');

        return $this;
    }

    /**
     * Select api endpoint url
     *
     * @return $this
     */
    private function setApiEndpointUrl()
    {
        $endpointTest = $this->container->getParameter('paysimple.api.endpoint.test');
        $endpointLive = $this->container->getParameter('paysimple.api.endpoint.live');
        $isTestMode = $this->container->getParameter('paysimple.api.test_mode');
        self::$apiEndpoint = $isTestMode === true ? $endpointTest : $endpointLive;

        return $this;
    }

    /**
     * Generate && set PaySimple Server Token to curl resource
     *
     * @return $this
     */
    private function generateServerToken()
    {
        $timestamp = gmdate("c");
        $hmac = hash_hmac("sha256", $timestamp, $this->apiSecretKey, true); //note the raw output parameter
        $hmac = base64_encode($hmac);
        $auth = "Authorization: PSSERVER AccessId = $this->login; Timestamp = $timestamp; Signature = $hmac";

        $this->curl->setHeaders([$auth, 'Content-Type: application/json; charset=utf-8']);

        return $this;
    }
}
