<?php

use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use App\Seller;
use App\Buyer;
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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified'=>$verificado=$faker->randomElement([User::USUARIO_VERIFICADO,User::USUARIO_NO_VERIFICADO]),
        'verification_token'=>$verificado==User::USUARIO_VERIFICADO?null:User::generaVerificationToken(),
        'admin'=>$faker->randomElement([User::USUARIO_ADMINISTRADOR,User::USUARIO_REGULAR]),
    ];
});


$factory->define(Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1),
    ];
});

$factory->define(Product::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1),
        'quantity'=>$faker->numberBetween(1,10),
        'status'=>$faker->randomElement([Product::PRODUCTO_DISPONIBLE,Product::PRODUCTO_NO_DISPONIBLE]),
        'image'=>$faker->randomElement(['icon.png','logo.png','logo_b.png']),
        //'seller_id'=>User::inRandomOrder()->first()->id,
        'seller_id'=>User::all()->random()->first()->id,
    ];
});

$factory->define(Transaction::class, function (Faker\Generator $faker) {
    $seller = Seller::has('products')->get()->random();
    $buyer = User::all()->except($seller->id)->random();

    return [
        'quantity' => $faker->numberBetween(1, 10),
        'buyer_id' => $buyer->id,
        'product_id' => $seller->products->random()->id
    ];
});