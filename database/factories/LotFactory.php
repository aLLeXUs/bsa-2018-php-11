<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Entity\Lot::class, function (Faker $faker) {
    return [
        'currency_id' => function () {
            return factory(App\Entity\Currency::class)->create()->id;
        },
        'seller_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'date_time_open' => Carbon::now(),
        'date_time_close' => Carbon::tomorrow(),
        'price' => $faker->randomFloat(2, 0, 100000)
    ];
});
