<?php

namespace App\GraphQL\Mutations;

final class Logout
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        request()->user()->currentAccessToken()->delete();

        return ['message' => "Token deleted"];
    }
}
