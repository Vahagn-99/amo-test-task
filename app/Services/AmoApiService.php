<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\BadTypeException;
use Exception;
use Illuminate\Support\Facades\Log;
use JsonException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class AmoApiService
{
    /** @var AmoCRMApiClient
     * api apiClient for work with amo
     */
    protected AmoCRMApiClient $apiClient;

    /**
     * @var mixed|null
     */
    protected mixed $code;

    /**
     * @var string
     */
    protected string $domain = 'vahagntxa.amocrm.ru';

    public function __construct()
    {
        $clientId = config('services.amo.client_id');
        $clientSecret = config('services.amo.client_secret');
        $redirectUri = config('services.amo.redirect_uri');

        $this->apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
    }

    /**
     * @throws BadTypeException
     * @throws Exception
     */
    public function showButton(): void
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;

        echo $this->apiClient->getOAuthClient()->getOAuthButton(
            [
                'title' => 'Установить интеграцию',
                'compact' => true,
                'class_name' => 'className',
                'color' => 'default',
                'error_callback' => 'handleOauthError',
                'state' => $state,
            ]
        );
    }

    public function setCode($code = null): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * The First thing which could do when need to connect to api service
     * @throws JsonException|AmoCRMoAuthApiException
     */
    public function authClient(): AmoCRMApiClient
    {
        $auth = isset($this->code) ?: null;

        $oauth = $this->apiClient->getOAuthClient();

        $oauth->setBaseDomain($this->domain);

        $accessToken = $this->getToken() ?: $oauth->getAccessTokenByCode($auth);
        try {
            $this->apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain($this->domain)
                ->onAccessTokenRefresh(
                    function (AccessTokenInterface $accessToken, string $baseDomain) {
                        $this->saveToken([
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]);
                    }
                );
            if (!$accessToken->hasExpired()) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $this->apiClient->getAccountBaseDomain(),
                ]);
            }
        } catch (JsonException $e) {
            Log::debug($e->getMessage());
        } finally {
            return $this->apiClient;
        }
    }

    /**
     * save token in local storage
     * @param array $accessToken
     * @return void
     * @throws JsonException
     */
    abstract protected function saveToken(array $accessToken): void;

    /**
     * get token from local storage
     * @return AccessToken|false
     * @throws JsonException
     */
    abstract protected function getToken(): AccessToken|false;
}
