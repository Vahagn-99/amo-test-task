<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JsonException;
use League\OAuth2\Client\Token\AccessToken;

trait AccessTokenManager
{

    /**
     * save token in local storage
     * @param array $accessToken
     * @return void
     * @throws JsonException
     */
    protected function saveToken(array $accessToken): void
    {
        if (
            isset($accessToken['accessToken']
                , $accessToken['refreshToken']
                , $accessToken['expires']
                , $accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];
            if (Storage::exists('tokens/tokens.txt')) {
                Storage::disk('local')->delete('tokens/tokens.txt');
            }
            Storage::disk('local')->put('tokens/tokens.txt', json_encode($data, JSON_THROW_ON_ERROR));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    /**
     * get token from local storage
     * @return AccessToken|false
     * @throws JsonException
     */
    protected function getToken(): AccessToken|false
    {
        if (!Storage::exists('tokens/tokens.txt') && !File::exists('token.txt')) {
            return false;
        }

        $accessToken = json_decode((Storage::get('tokens/tokens.txt') ?: File::get('token.txt')), true, 512,
            JSON_THROW_ON_ERROR);

        if (
            isset($accessToken['accessToken']
                , $accessToken['refreshToken']
                , $accessToken['expires']
                , $accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        }

        exit('Invalid access token ' . var_export($accessToken, true));
    }
}
