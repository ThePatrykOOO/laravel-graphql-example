<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthGraphQLTest extends TestCase
{
    use RefreshDatabase;


    public function testAuthUserSuccess(): void
    {
        User::factory()->create(
            [
                'email' => "test@example.com"
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              login(
                email: "test@example.com", 
                password: "password",
                device_name: "ip"
              )
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'login'
                ]
            ]
        );
    }

    public function testAuthUserIncorrectPassword(): void
    {
        User::factory()->create(
            [
                'email' => "test@example.com"
            ]
        );

        $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              login(
                email: "test@example.com", 
                password: "passw2ord",
                device_name: "ip"
              )
            }'
        )->assertGraphQLValidationError("email", "The provided credentials are incorrect.");
    }

    public function testAuthMeSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            query AuthMe {
              me {
                id,
                name,
                email
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'me' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ]
        );
    }
}