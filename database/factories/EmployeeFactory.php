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

const SALARY_MIN = 40000;
const SALARY_MAX = 250000;

$factory->define(Employee::class, function (Faker $faker) {
    $positionIds = Position::get()
        ->pluck('id')
        ->toArray();

    return [
        'position_id' => $faker->randomElement($positionIds),
        'first_name' => $faker->firstName,
        'last_name'  => $faker->lastName,
        'salary'     => $faker->numberBetween(SALARY_MIN, SALARY_MAX),
        'is_active'  => $faker->boolean,
        'hired_date' => $faker->dateTime,
    ];
});
