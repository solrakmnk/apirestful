<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionCategoryController extends ApiController
{

    public function index(Transaction $transaction)
    {
        $categories=$transaction->product->categories;
        return $this->showAll($categories);
    }
}
