<?php

use Faker\Generator as Faker;

$factory->define(CodeShopping\Models\ChatGroupInvitation::class, function (Faker $faker) {
    return [
        'total' => $faker->numberBetween(2,5),
        'expires_at' => rand(1,10) % 2 == 0 ? null : $faker->dateTimeBetween('+ 7 days', '+ 20 days')
    ];
});
