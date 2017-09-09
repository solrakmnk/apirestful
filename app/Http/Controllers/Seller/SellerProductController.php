<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products=$seller->products;
        return $this->showAll($products);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,User $seller) //Por si aun no es vendedor se manda user
    {
        $rules=[
            'name'=>'required',
            'description'=>'required',
            'quantity'=>'required|integer|min:1',
            'image'=>'required|image'
        ];
        $this->validate($request,$rules);

        $data=$request->all();

        $data['status']=Product::PRODUCTO_NO_DISPONIBLE;
        $data['image']='icon.png';
        $data['seller_id']=$seller->id;

        $product=Product::create($data);
        $product->save();

        return $this->showOne($product,201);


    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller,Product $product)
    {
        $rules=[
            'quantity'=>'integer|min:1',
            'status'=>'in:'.Product::PRODUCTO_NO_DISPONIBLE.','.Product::PRODUCTO_DISPONIBLE,
            'image'=>'image'
        ];
        $this->validate($request,$rules);

        if($seller->id!=$product->seller_id){
            return $this->errorResponse('El vendedor especificado no es el vendedor del producto',422);
        }


        $product->fill($request->intersect([
            'name',
            'description',
            'quantity'
        ]));

        if($request->has('status')){
            $product->status=$request->status;

            if($product->estaDisponible() && $product->categories()->count()==0){
                return $this->errorResponse('Un producto activo debe tener al menos una categoria',422);
            }


        }

        if($product->isClean()){
            return $this->errorResponse("Debe especificar al menos un cambio",422);
        }

        $product->save();

        return $this->showOne($product,201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller)
    {
        //
    }
}
