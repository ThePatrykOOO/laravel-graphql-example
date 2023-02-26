<?php

namespace Tests\Feature\GraphQL;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepartmentGraphQLTest extends TestCase
{
    use RefreshDatabase;


    public function testQueryDepartments(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        Department::factory(4)->create();

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            query {
              departments(first: 10) {
                data {
                  id
                  name
                  address
                }
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'departments' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'address'
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertCount(4, $response->json('data.departments.data'));
    }

    public function testQuerySingleDepartment(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $department = Department::factory()->create();

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            {
              department(id: "'.$department->id.'") {
                id
                name
                address
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'department' => [
                        'id',
                        'name',
                        'address'
                    ]
                ]
            ]
        );

        $this->assertEquals($department->id, $response->json('data.department.id'));
    }

    public function testMutationCreateDepartment(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              createDepartment(
                name: "ED Company",
                address: "Long Street 500"
              ) {
                id
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'createDepartment' => [
                        'id',
                    ]
                ]
            ]
        );

        $this->assertCount(1, Department::all());
    }

    public function testMutationUpdateDepartment(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $department = Department::factory()->create(
            [
                'name' => 'Old Department',
                'address' => 'Old Street',
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              updateDepartment(
                id: '.$department->id.',
                name: "new department",
                address: "new street"
              ) {
                id
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'updateDepartment' => [
                        'id',
                    ]
                ]
            ]
        );

        $department->refresh();

        $this->assertEquals("new department", $department->name);
        $this->assertEquals("new street", $department->address);
    }

    public function testMutationDeleteDepartment(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $department = Department::factory()->create();

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              deleteDepartment(id: '.$department->id.') {
                id
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'deleteDepartment' => [
                        'id',
                    ]
                ]
            ]
        );

        $this->assertCount(0, Department::all());
    }


}
