<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Profile::class, function (Faker\Generator $faker) {
    return [
        'prefix_id' => 1,
        'name' => $faker->firstNameMale,
        'lastName' => $faker->lastName,
        'address' => $faker->streetAddress,
        'subdistrict_id' => 1,
        'postcode_id' => 9,
        'birth_date' => Diamond::today(),
    ];
});

$factory->define(App\Member::class, function (Faker\Generator $faker) {
    return [
        'start_date' => Diamond::today(),
        'shareholding_date' => Diamond::today(),
        'leave_date' => Diamond::today(),
    ];
});

$factory->define(App\Employee::class, function (Faker\Generator $faker) {
    return [
        'employee_type_id' => 1,
        'start_date' => Diamond::today(),
        'leave_date' => Diamond::today(),
    ];
});
