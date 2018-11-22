<?php

use App\Employee;
use App\Position;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Position::class, function (Faker $faker) {
    $employeeIds = Employee::get()
        ->pluck('id')
        ->toArray();
    return [
        'employee_id' => $faker->randomElement($employeeIds),
        'title' => $faker->unique()->jobTitle,
    ];
});
