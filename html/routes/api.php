<?php

use App\Http\Controllers\Api\V1\Admin\TourController as AdminTourController;
use App\Http\Controllers\Api\V1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
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

// https://www.dropbox.com/oauth2/authorize?client_id=idid1a09ipe1q4l&token_access_type=offline&response_type=code&redirect_uri=http://127.0.0.1:8000

Route::get('test-email', [TravelController::class,'processQueue']);
Route::post('/import-excel', [TravelController::class,'importExcel'])->name('importExcel');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::get('travels',[TravelController::class,'index']);
    Route::get('travels/{travel:slug}/tours',[TourController::class,'index']);
});
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::post('travels',[AdminTravelController::class,'store'])->name('travelsAdmin');
        Route::post('travels/{travels}/tours',[AdminTourController::class,'store'])->name('tourAdmin');
    });
    Route::put('travels/{travels}',[AdminTravelController::class,'update']);
});

Route::post('login',[LoginController::class,'index']);