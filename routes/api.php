<?php

use App\Http\Controllers\AUthController;
use App\Http\Controllers\DrugsController;
use App\Http\Controllers\OrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('listAllWarehouses',[DrugsController::class,'listAllWarehouses']);

Route::post('createNewDrug',[DrugsController::class,'createNewDrug'])->middleware('verifyApiToken');
Route::get('listAllDrugs/{warehouse_id}',[DrugsController::class,'listAllDrugs'])->middleware('verifyApiToken');
Route::post('search',[DrugsController::class,'search'])->middleware('verifyApiToken');
Route::post('listDetails/{warehouse_id}/{drug_id}',[DrugsController::class,'listDetails'])->middleware('verifyApiToken');
Route::post('sort',[DrugsController::class,'sort']);



Route::post('createNewOrder',[OrdersController::class,'createNewOrder'])->middleware('verifyApiToken');
Route::get('listMyOreders',[OrdersController::class,'listMyOreders'])->middleware('verifyApiToken');
Route::get('listAllOreders',[OrdersController::class,'listAllOreders'])->middleware('verifyApiToken');
Route::post('editOreder',[OrdersController::class,'editOreder'])->middleware('verifyApiToken');

Route::post('register',[AuthController::class,'register'])->middleware('register_check');
Route::post('login',[AuthController::class,'login'])->middleware(['login_check']);
