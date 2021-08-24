<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController ;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SocialController;
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

    Route::put('/forgetPassword',[UserController::class,'forgetPassword']);

    Route::post('/editUser',[UserController::class,'editUser']);
    Route::get('/getUser/{id?}',[UserController::class,'getUser']);

    Route::post('/createPost',[PostController::class,'create']);
    Route::put('/editPost/{posts}',[PostController::class,'editPost']);
    Route::get('/showPost/{posts}',[PostController::class,'show']);
    Route::delete('/deletePost/{posts}',[PostController::class,'deletePost']);
    Route::post('/favourite',[PostController::class,'favourite']);

    Route::post('/addFriend',[UserController::class,'addFriend']);
    Route::get('/friendlist',[UserController::class,'friendlist']);


    Route::post('/like',[PostController::class,'like']);

    Route::post('/comment',[PostController::class,'comment']);
    Route::put('/editComment/{comments}',[PostController::class,'editComment']);
    Route::delete('/deleteComment/{comments}',[PostController::class,'deleteComment']);
    Route::get('/showComment/{posts}',[PostController::class,'showComment']);

    Route::get('/timeLine',[PostController::class,'timeLine']);
    Route::get('/homePage/{id?}',[PostController::class,'homePage']);

    Route::get('auth/facebook', [SocialController::class, 'facebookRedirect']);

    Route::get('auth/facebook/callback', [SocialController::class, 'loginWithFacebook']);

    Route::post('logout',[UserController::class,'logout']);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
