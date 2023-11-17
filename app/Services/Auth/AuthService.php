<?php

namespace App\Services\Auth;

use App\Components\HttpClient\HttpClientInterface;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function setUserToSession(Authenticatable $user): void
    {
        /** @var User $user */
        session(['user' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ]]);
    }

    public function setJwtCookieToResponse(): void
    {
        $jwt = $this->getJwt();
        $this->setCookie($jwt);
    }

    /**
     * @return array{token_type: string, expires_in: int, access_token: string}
     */
    protected function getJwt(): array
    {
        $jwt = $this->httpClient
            ->setMethod('POST')
            ->setQuery(request(['email', 'password']))
            ->setUri(route('back.api.auth.login', '', false))
            ->send()
            ->getBody()
            ->getContents();
        $jwt = json_decode($jwt, true);
        /** @var array{token_type: string, expires_in: int, access_token: string} */
        return $jwt;
    }

    /**
     * @param array{token_type: string, expires_in: int, access_token: string} $jwt
     * @return void
     */
    protected function setCookie(array $jwt): void
    {
        $config = config('session');

        cookie()->queue(
            'jwt',
            $jwt['token_type'] . ' ' . $jwt['access_token'],
            $jwt['expires_in'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null
        );
    }

    public function jwtInvalidate(): void
    {
        $this->httpClient
            ->setMethod('POST')
            ->setUri(route('back.api.auth.logout', '', false))
            ->send();
    }
}
