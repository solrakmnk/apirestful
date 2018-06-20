<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;


class BuyerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general')->only('show');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buyers=Buyer::has('transactions')->get();
        return $this->showAll($buyers,200);

    }

    public function  show(Buyer $buyer)
    {
        return $this->showOne($buyer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id)
//    {
//        $buyer=Buyer::has('transactions')->findOrFail($id);
//        return $this->showOne($buyer,200);
//    }
}
