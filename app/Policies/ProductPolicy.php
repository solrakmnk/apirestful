<?php

namespace App\Policies;

use App\User;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;
    
    public function addCategory(User $user, Product $product)
    {
        return $user->id===$product->seller->id;
    }


    public function deleteCategory(User $user, Product $product)
    {
        return $user->id===$product->seller->id;
    }

}
