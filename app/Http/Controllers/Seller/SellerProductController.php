<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\Transformers\ProductTransformer;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:'.ProductTransformer::class)->only(['store','update']);
        $this->middleware('scope:manage-products')->except('index');
    }
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
        $data['image']=$request->image->store('');//sin nombre de archivo laravel genera un nombre aleatorio unico images ya esta default
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

        $this->verificarVendedor($seller,$product);

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

        if($request->hasFile('image')){
            Storage::delete($product->image);
            $product->image=$request->image->store('');
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
    public function destroy(Seller $seller,Product $product)
    {
        $this->verificarVendedor($seller,$product);

        Storage::delete($product->image);

        $product->delete();

        return $this->showOne($product);
    }

    protected function verificarVendedor(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }
}
