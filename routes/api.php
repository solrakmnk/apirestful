<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::resource('buyers','Buyer\BuyerController',['only'=>['index','show']]);
Route::resource('buyers.transactions','Buyer\BuyerTransactionController',['only'=>['index']]);
Route::resource('buyers.products','Buyer\BuyerProductController',['only'=>['index']]);
Route::resource('buyers.sellers','Buyer\BuyerSellerController',['only'=>['index']]);
Route::resource('buyers.categories','Buyer\BuyerCategoryController',['only'=>['index']]);


Route::resource('categories','Category\CategoryController',['except'=>['create','edit']]);
Route::resource('categories.product','Category\CategoryProductController',['only'=>['index']]);
Route::resource('categories.seller','Category\CategorySellerController',['only'=>['index']]);
Route::resource('categories.Transaction','Category\CategoryTransactionController',['only'=>['index']]);
Route::resource('categories.Buyer','Category\CategoryBuyerController',['only'=>['index']]);


Route::resource('products','Product\ProductController',['only'=>['index','show']]);
Route::resource('products.transactions','Product\ProductTransactionController',['only'=>['index']]);
Route::resource('products.buyer','Product\ProductBuyerController',['only'=>['index','store']]);
Route::resource('products.category','Product\ProductCategoryController',['only'=>['index','update','destroy']]);

Route::resource('transactions','Transaction\TransactionController',['only'=>['index','show']]);
Route::resource('transactions.categories','Transaction\TransactionCategoryController',['only'=>['index']]);
Route::resource('transactions.sellers','Transaction\TransactionSellerController',['only'=>['index']]);

Route::resource('sellers','Seller\SellerController',['only'=>['index','show']]);
Route::resource('sellers.transactions','Seller\SellerTransactionController',['only'=>['index']]);
Route::resource('sellers.category','Seller\SellerCategoryController',['only'=>['index']]);
Route::resource('sellers.buyer','Seller\SellerBuyerController',['only'=>['index']]);
Route::resource('sellers.product','Seller\SellerBuyerController',['except'=>['create','edit']]);


Route::resource('users','User\UserController',['except'=>['create','edit']]);
