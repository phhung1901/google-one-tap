<?php
namespace GoogleOneTap\Services;

use Google\Client;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class GoogleOneTap extends AbstractProvider implements ProviderInterface
{
    protected function getUserByToken($token): array
    {
        $client = $this->getClient();
        $info = $client->verifyIdToken($token);
        if (!$info) {
            throw new \Exception('Invalid token');
        }

        return $info;
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => Arr::get($user, 'sub'),
            'nickname' => Arr::get($user, 'nickname'),
            'name' => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email'),
            'avatar' => $avatarUrl = Arr::get($user, 'picture'),
            'avatar_original' => $avatarUrl,
            'given_name' => Arr::get($user, 'given_name'),
            'family_name' => Arr::get($user, 'family_name'),
        ]);
    }

    private function getClient(): Client
    {
        return new Client([
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
        ]);
    }

    protected function getAuthUrl($state)
    {
        // TODO: Implement getAuthUrl() method.
    }

    protected function getTokenUrl()
    {
        // TODO: Implement getTokenUrl() method.
    }
}
