<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', [AuthController::class, 'login']);
Route::post('/transaction', [TransactionController::class, 'index']);

Route::middleware(['jwt.verify'])->group(function () {
  // Authentication
  Route::post('logout', [AuthController::class, 'logout']);
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::get('me', [AuthController::class, 'me']);

  // Customer
  Route::get('/customer', [CustomerController::class, 'index']);
  Route::get('/customer/detail', [CustomerController::class, 'detail']);
  Route::post('/customer', [CustomerController::class, 'create']);
  Route::put('/customer', [CustomerController::class, 'update']);
  Route::delete('/customer', [CustomerController::class, 'delete']);
  Route::get('/customer/transaction', [CustomerController::class, 'customer_transaction']);

  // Product
  Route::get('/product', [ProductController::class, 'product']);
  Route::get('/product_detail', [ProductController::class, 'index']);
  Route::post('/product_detail', [ProductController::class, 'create']);
  Route::delete('/product_detail', [ProductController::class, 'delete']);
  Route::get('/product_code', [ProductController::class, 'product_code']);
  
  // Transaction
  Route::get('/history_transaction', [TransactionController::class, 'history']);
  Route::get('/history_transaction_detail', [TransactionController::class, 'history_detail']);
  Route::get('/download_struk', [TransactionController::class, 'download_struk']);
  // Route::get('/transaction/export', [TransactionController::class, 'export_transaction']);
});

Route::get('/transaction/export', [TransactionController::class, 'export_transaction']);