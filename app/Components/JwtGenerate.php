<?php

namespace App\Components;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class JwtGenerate
{

    public static function JwtGenerate(Request $request): void
    {
        $client = new Client(config('guzzle'));
        $token = $client->request('post', 'api/auth/login', ['query' => ['email' => $request->email, 'password' => $request->password]])->getBody()->getContents();
        $token = json_decode($token, true);
        $token = $token['token_type'] . ' ' . $token['access_token'];
        session(['jwt' => $token]);
    }
}
