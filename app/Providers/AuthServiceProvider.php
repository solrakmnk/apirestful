<?php

namespace App\Providers;

use App\Buyer;
use App\Policies\BuyerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SellerPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Product;
use App\Seller;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
       // 'App\Model' => 'App\Policies\ModelPolicy',
        Buyer::class=>BuyerPolicy::class,
        Seller::class=>SellerPolicy::class,
        User::class=>UserPolicy::class,
        Transaction::class=>TransactionPolicy::class,
        Product::class=>ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));//30 dias para actualizar el token original, si no se tiene que hacer flujo de autorizacion
        Passport::enableImplicitGrant();
        Passport::tokensCan([
            'purchase-product'=>'Crear Transacciones para comprar productos determinados',
            'manage-products'=>'Crear, ver, actualizar y eliminar productos',
            'manage-account'=>'Obtener informacion de la cuenta, nombre, email, estado, sin contraseña, 
                                modificar datos como email , nombre y contraseña,No puede eliminar cuenta',
            'read-general'=>'Obtener informacion general, categorias donde se compra y se vende,
                            productos vendidos o comprados, transacciones, compras y ventas'
            ]);
    }
}
