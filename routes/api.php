<?php

use App\Http\Controllers\CategoryManagementController;
use App\Http\Controllers\ProductManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Colors\Rgb\Channels\Red;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(CategoryManagementController::class)->group(function () {
    Route::post('insert_category', 'insertCategoryData');
    Route::post('update_category', 'updateCategoryData');
    Route::get('get_category', 'getCategory');
    Route::get('get_category_by_id', 'getCategoryDataByID');
    Route::get('get_categories', 'getCategoryDropdown');
});

Route::controller(ProductManagementController::class)->group(function () {
    Route::post('insert_product', 'insertProductData');
    Route::post('update_product', 'updateProductData');
    Route::get('get_product', 'getProduct');
    Route::get('get_product_by_id', 'getProductDataByID');
});
