<?php

namespace NotificationChannels\Lox24;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use NotificationChannels\Lox24\Exceptions\CouldNotSendNotification;

class Lox24
{
    /** @var HttpClient HTTP Client */
    protected $http;

    /** @var null|string Lox24 Account ID. */
    protected $accountId = null;

    /** @var null|string Lox24 Password Hash. */
    protected $passwordHash = null;

    /**
     * @param null            $accountId
     * @param null            $password
     * @param HttpClient|null $httpClient
     */
    public function __construct($accountId = null, $password = null, HttpClient $httpClient = null)
    {
        $this->accountId    = $accountId;
        $this->passwordHash = md5($password);
        $this->http         = $httpClient;
    }

    /**
     * Get HttpClient.
     *
     * @return HttpClient
     */
    protected function httpClient()
    {
        return $this->http ?: $this->http = new HttpClient();
    }

    /**
     * Send SMS
     *
     * <code>
     * $params = [
     *   'service'                  => '',
     *   'text'                     => '',
     *   'encoding'                 => '',
     *   'from'                     => '',
     *   'timestamp'                => '',
     *   'return'                   => '',
     *   'httphead'                 => '',
     *   'action'                   => '',

     * ];
     * </code>
     *
     * @link https://www.lox24.eu/api/LOX24-SMS-API.pdf
     *
     * @param array $params
     *
     * @var int        $params ['service']
     * @var string     $params ['text']
     * @var string     $params ['to']
     * @var int        $params ['encoding']
     * @var string     $params ['from']
     * @var int        $params ['timestamp']
     * @var string     $params ['return']
     * @var bool       $params ['httphead']
     * @var string     $params ['action']
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws CouldNotSendNotification
     */
    public function sendMessage($params)
    {
        if (empty($this->accountId)) {
            throw new CouldNotSendNotification('You must provide your Lox24 Account ID to make any API requests.');
        }

        if (empty($this->accountId)) {
            throw new CouldNotSendNotification('You must provide your Lox24 Password to make any API requests.');
        }

        $params['account'] = $this->accountId;
        $params['passwordHash'] = $this->passwordHash;


        $endPointUrl = 'https://www.lox24.eu/API/httpsms.php';

        try {
            return $this->httpClient()->post($endPointUrl, [
                'form_params' => $params,
            ]);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::lox24RespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw new CouldNotSendNotification($exception->getMessage());
        }
    }
}