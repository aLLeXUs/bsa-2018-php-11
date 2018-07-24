<?php

use Faker\Generator as Faker;

$factory->define(App\Entity\Trade::class, function (Faker $faker) {
    return [
        "lot_id" => function () {
            return factory(App\Entity\Lot::class)->create()->id;
        },
        "user_id" => function () {
            return factory(App\User::class)->create()->id;
        },
        "amount" => $faker->randomFloat(2, 0, 100000)
    ];
});
