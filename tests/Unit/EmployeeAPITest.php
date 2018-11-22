<?php

namespace Tests\Unit;

use App\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeAPITest extends TestCase
{
    use withFaker;

    const ENDPOINT = '/api/employee';
    const JSON_SUCCESS = ['success'];
    const JSON_SUCCESS_WITH_DATA = ['success', 'data'];
    const JSON_SUCCESS_WITH_MSG = ['success', 'message'];


    /**
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(self::ENDPOINT);

        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_DATA)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     *
     * @return void
     */
    public function testStoreEmptyInput()
    {
        $response = $this->json('POST', self::ENDPOINT, []);
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_MSG)
            ->assertJson([
                'success' => false,
                'message' => [
                    'first_name' => ['The first name field is required.'],
                    'last_name'  => ['The last name field is required.'],
                    'salary'     => ['The salary field is required.'],
                    'hired_date' => ['The hired date field is required.'],
                ]
            ]);
    }

    /**
     *
     * @return void
     */
    public function testStoreInvalidFirstName()
    {
        $data =
            [
                'first_name' => ['INVALID'],
                'last_name'  => $this->faker->lastName,
                'salary'     => $this->faker->randomDigit,
                'hired_date' => $this->faker->date('Y-m-d h:i:s'),
                'title'      => $this->faker->jobTitle,
            ];

        $response = $this->json('POST', self::ENDPOINT, $data);
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_MSG)
            ->assertJson([
                'success' => false,
                'message' => [
                    'first_name' => ['The first name must be a string.']
                ]
            ]);
    }

    /**
     *
     * @return void
     */
    public function testStoreInvalidLastName()
    {
        $data =
            [
                'first_name' => $this->faker->lastName,
                'last_name'  => ['INVALID'],
                'salary'     => $this->faker->randomDigit,
                'hired_date' => $this->faker->date('Y-m-d h:i:s'),
                'title'      => $this->faker->jobTitle
            ];

        $response = $this->json('POST', self::ENDPOINT, $data);
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_MSG)
            ->assertJson([
                'success' => false,
                'message' => [
                    'last_name' => ['The last name must be a string.']
                ]
            ]);
    }

    /**
     *
     * @return void
     */
    public function testStoreInvalidSalary()
    {
        $data =
            [
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'salary'     => $this->faker->randomAscii,
                'hired_date' => $this->faker->date('Y-m-d h:i:s'),
                'title'      => $this->faker->jobTitle
            ];

        $response = $this->json('POST', self::ENDPOINT, $data);
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_MSG)
            ->assertJson([
                'success' => false,
                'message' => [
                    'salary' => ['The salary must be an integer.']
                ]
            ]);
    }

    /**
     *
     * @return void
     */
    public function testStoreInvalidDate()
    {
        $data =
            [
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'salary'     => $this->faker->randomDigit,
                'hired_date' => $this->faker->randomAscii,
                'title'      => $this->faker->jobTitle
            ];

        $response = $this->json('POST', self::ENDPOINT, $data);
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_MSG)
            ->assertJson([
                'success' => false,
                'message' => [
                    'hired_date' => ['The hired date is not a valid date.']
                ]
            ]);
    }

    /**
     *
     * @return void
     */
    public function testStoreMissingTitle()
    {
        $data =
            [
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'salary'     => $this->faker->randomDigit,
                'hired_date' => $this->faker->date('Y-m-d h:i:s')
            ];

        $response = $this->json('POST', self::ENDPOINT, $data);
        $response
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonStructure(self::JSON_SUCCESS)
            ->assertJson(['success' => true,]);
    }

    /**
     *
     * @return void
     */
    public function testStoreValidInput()
    {
        $data =
            [
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'salary'     => $this->faker->randomDigit,
                'hired_date' => $this->faker->date('Y-m-d h:i:s'),
                'title'      => $this->faker->jobTitle
            ];

        $response = $this->json('POST', self::ENDPOINT, $data);
        $response
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonStructure(self::JSON_SUCCESS)
            ->assertJson(['success' => true,]);
    }

    /**
     *
     * @return void
     */
    public function testShowValidEmployeeId()
    {
        $employee = Employee::first();
        $response = $this->get(self::ENDPOINT . "/$employee->id");
        $employee = $employee->toArray();
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_DATA)
            ->assertJson([
                'success' => true,
                'data'    => $employee
            ]);
    }

    /**
     *
     * @return void
     */
    public function testShowInvalidEmployeeId()
    {

        $response = $this->get(self::ENDPOINT . '/' . PHP_INT_MAX);

        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_MSG)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot find employee with ID: ' . PHP_INT_MAX,
            ]);
    }

    /**
     *
     * @return void
     */
    public function testUpdateEmptyInput()
    {
        $employee = Employee::first();
        $response = $this->json('PUT', self::ENDPOINT . "/$employee->id", []);
        $employee = $employee->toArray();
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_DATA)
            ->assertJson([
                'success' => true,
                'data'    => $employee
            ]);
    }

    /**
     *
     * @return void
     */
    public function testUpdateValidInput()
    {
        $employee = Employee::first();
        $data =
            [
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'salary'     => $this->faker->randomDigit,
                'hired_date' => $this->faker->date('Y-m-d h:i:s'),
            ];

        $response = $this->json('PUT', self::ENDPOINT . "/$employee->id", $data);
        $employee = $employee->refresh()->toArray();
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS_WITH_DATA)
            ->assertJson([
                'success' => true,
                'data'    => $employee
            ]);

        $this->assertEquals($data['first_name'], $employee['first_name']);
        $this->assertEquals($data['last_name'], $employee['last_name']);
        $this->assertEquals($data['salary'], $employee['salary']);
        $this->assertEquals($data['hired_date'], $employee['hired_date']);
    }

    /**
     *
     * @return void
     */
    public function testDestroyValidEmployeeId()
    {
        $employee = Employee::first();
        $response = $this->json('DELETE', self::ENDPOINT . "/$employee->id");
        $response
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonStructure(self::JSON_SUCCESS)
            ->assertJson([
                'success' => true,
            ]);

        $employee = Employee::find($employee->id);
        $this->assertNull($employee);
    }

    /**
     *
     * @return void
     */
    public function testDestroyInvalidEmployeeId()
    {
        $response = $this->json('DELETE', self::ENDPOINT . '/' . PHP_INT_MAX);
        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure(self::JSON_SUCCESS)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot find employee with ID: ' . PHP_INT_MAX,
            ]);
    }
}
