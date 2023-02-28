<?php

namespace Tests\Feature\GraphQL;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeeGraphQLTest extends TestCase
{
    use RefreshDatabase;


    public function testQueryEmployees(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        Employee::factory(4)->create(
            [
                'department_id' => Department::factory()->create()->id
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            {
              employees(first: 10) {
                data {
                  id
                  first_name
                  last_name
                }
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'employees' => [
                        'data' => [
                            '*' => [
                                'id',
                                'first_name',
                                'last_name'
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertCount(4, $response->json('data.employees.data'));
    }

    public function testQueryEmployeesRelationDepartment(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        Employee::factory(4)->create(
            [
                'department_id' => Department::factory()->create()->id
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            {
              employees(first: 10) {
                data {
                  id
                  first_name
                  last_name
                  department {
                    id
                  }
                }
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'employees' => [
                        'data' => [
                            '*' => [
                                'id',
                                'first_name',
                                'last_name',
                                'department' => [
                                    'id'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertCount(4, $response->json('data.employees.data'));
    }

    public function testQuerySingleEmployee(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $employee = Employee::factory()->create(
            [
                'department_id' => Department::factory()->create()->id
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            {
              employee(id: "'.$employee->id.'") {
                id
                first_name
                last_name
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'employee' => [
                        'id',
                        'first_name',
                        'last_name'
                    ]
                ]
            ]
        );

        $this->assertEquals($employee->id, $response->json('data.employee.id'));
    }

    public function testMutationCreateEmployee(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $department = Department::factory()->create();

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              createEmployee(
                department_id: '.$department->id.'
                first_name: "john"
                last_name: "doe"
                role: DEVELOPER
                usd_salary: 1000
              ) {
                id
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'createEmployee' => [
                        'id',
                    ]
                ]
            ]
        );

        $this->assertCount(1, Employee::all());
    }

    public function testMutationUpdateEmployee(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $department = Department::factory()->create();

        $employee = Employee::factory()->create(
            [
                'first_name' => "Mark",
                'last_name' => "XYZ",
                'department_id' => $department->id,
                'role' => 'developer',
                'usd_salary' => 5000,
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              updateEmployee(
                id: '.$employee->id.',
                first_name: "john"
                department_id: '.$department->id.',
                last_name: "doe"
                role: MANAGER
                usd_salary: 10000
              ) {
                id
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'updateEmployee' => [
                        'id',
                    ]
                ]
            ]
        );

        $employee->refresh();

        $this->assertEquals("john", $employee->first_name);
        $this->assertEquals("doe", $employee->last_name);
        $this->assertEquals("manager", $employee->role);
        $this->assertEquals(10000, $employee->usd_salary);
    }

    public function testMutationDeleteEmployee(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
        );

        $department = Department::factory()->create();

        $employee = Employee::factory()->create(
            [
                'department_id' => $department->id,
            ]
        );

        $response = $this->graphQL(
        /** @lang GraphQL */ '
            mutation {
              deleteEmployee(id: '.$employee->id.') {
                id
              }
            }'
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'deleteEmployee' => [
                        'id',
                    ]
                ]
            ]
        );

        $this->assertCount(0, Employee::all());
    }


}
