<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\BaseApiCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\AccountModel;
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

    public function __construct()
    {
        $this->apiClient = new AmoCRMApiClient(...$this->apiSettings());
    }

    private function apiSettings(): array
    {
        return (array)config('service.amo');
    }

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
     * The First thing which could do when need to connect to amocrm api service
     */
    public function authClient(): AmoCRMApiClient
    {
        $auth = $this->code;
        try {
            $oauth = $this->apiClient->getOAuthClient();

            $oauth->setBaseDomain("provansme.amocrm.ru");

            $accessToken = $this->getToken() ?? $oauth->getAccessTokenByCode($auth);

            $this->apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain('provansme.amocrm.ru')
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
        } catch (\AmoCRM\Exceptions\AmoCRMoAuthApiException|JsonException $e) {
            Log::debug($e->getMessage());
        } finally {
            return $this->apiClient;
        }
    }

    /**
     * Get account properties with all available properties
     */
    public function getAmoAccount(): BaseApiCollection|AccountModel|null
    {
        /** @var  BaseApiCollection|AccountModel|null $account */
        $account = null;

        $this->authClient();
        try {
            $account = $this->apiClient->account()->getCurrent(AccountModel::getAvailableWith());
        } catch (AmoCRMApiException $e) {
            Log::debug($e->getMessage());
        } finally {
            return $account;
        }
    }

    /**settings
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
