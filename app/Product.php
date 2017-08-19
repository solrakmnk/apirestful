<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const PRODUCTO_DISPONIBLE='disponible';
    const PRODUCTO_NO_DISPONIBLE='no disponible';

    protected $fillable=[
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id'
    ];
    public function estaDisponible(){
        return $this->status==Prodcuct::PRODDUCTO_DISPONIBLE;
    }
}
