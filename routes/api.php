<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController ;
use App\Http\Controllers\PostController;
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
Route::post('register',[UserController::class,'Register']);
Route::post('login',[UserController::class,'login']);


Route::group(['middleware'=>'auth:api'],function (){
    Route::post('/putUser',[UserController::class,'update']);
    Route::post('/createPost',[PostController::class,'create']);
    Route::post('/addFriend',[UserController::class,'addFriend']);
    Route::get('/friendlist',[UserController::class,'friendlist']);
    Route::delete('/deletePost/{posts}',[PostController::class,'deletePost']);
    Route::put('/editPost/{posts}',[PostController::class,'editPost']);
    Route::get('/showPost/{posts}',[PostController::class,'show']);
    Route::post('/favourite',[PostController::class,'favourite']);
    Route::post('/like',[PostController::class,'like']);
    Route::post('/comment',[PostController::class,'comment']);
    Route::put('/editComment/{comments}',[PostController::class,'editComment']);
    Route::delete('/deleteComment/{comments}',[PostController::class,'deleteComment']);
    Route::get('/showComment/{posts}',[PostController::class,'showComment']);
    Route::get('/timeLine',[PostController::class,'timeLine']);

});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
