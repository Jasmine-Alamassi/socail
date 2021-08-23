<?php
/*
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{

    public function login(Request $request)
    {

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = Route::dispatch($proxy);

        $response = json_decode($response->getContent());

        $token = $response;

        $proxy = Request::create(
            'api/user',
            'get'
        );

        $proxy->headers->set('Authorization', 'Bearer '.$token->access_token);
        $response = Route::dispatch($proxy);

        $user = json_decode($response->getContent());


        $data = [
            'token' => $token,
            'user' => $user,
        ];
        return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Success', 'items' => $data]);
    }

    public function refreshToken(Request $request)
    {

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = Route::dispatch($proxy);

        $response = json_decode($response->getContent());


        return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Success', 'items' => $response]);
    }

    public function getUser($user_id = null)
    {
        if (isset($user_id)) {
            $user = User::find($user_id);
        } else
            $user = auth()->user();

        return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Success', 'items' => $user]);

    }

    public function postUser(Request $request)
    {
        $user = new User();
       // $user->fname = $request->get('fname');
        $user->lname = $request->get('name');
       // $user->phone = $request->get('phone');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();
        return response()->json(true);
    }

    public function putUser(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $user->fname = $request->get('fname');
        $user->lname = $request->get('lname');
        $user->phone = $request->get('phone');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();
        return response()->json(true);
    }
}
*/

namespace App\Http\Controllers\Api;

use App\ApiCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required|max:1000'
        ]);

        $validateData['password'] = bcrypt($request->password);
        $user = User::create($validateData);
        $accessToken = $user->createToken('authToken')->accessToken; ///authToken تم تسمية الtoken باي اسم
        return response(['user' => $user, 'access_token' => $accessToken]);

    }



    /**
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $proxy = Request::create(
            'oauth/token',
            'POST'
        );
//        $response =  app()->handle($proxy);

        $response = Route::dispatch($proxy);
        $token = json_decode($response->getContent());


        $request->headers->set('Authorization', 'Bearer' . $token->access_token);

        $proxy = Request::create(
            'api/user',
            'get'
        );

//        $response = app()->handle($proxy);
        $response = Route::dispatch($proxy);

        $user = json_decode($response->getContent());

        $data = [
            'token' => $token,
            'user' => $user,
        ];
        return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Success', 'items' => $data]);
    }

    public function refreshToken(Request $request)
    {

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = Route::dispatch($proxy);

        $response = json_decode($response->getContent());


        return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Success', 'items' => $response]);
    }

    public function getUser($user_id = null)
    {
        if (isset($user_id)) {
            $user = User::find($user_id);
        } else
            $user = auth()->user();

        return response()->json(['status' => true, 'statusCode' => 200, 'message' => 'Success', 'items' => $user]);

    }

    public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        return response()->json(["message" => 'If an account with the corresponding e-mail exists a password reset link will be send to the provided address.'], 200);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $reset_password_status = Password::reset($request->validated(), function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return $this->respondBadRequest(ApiCode::INVALID_RESET_PASSWORD_TOKEN);
        }

        return $this->respondWithMessage("Password has been successfully changed");
    }

    private function respondBadRequest($INVALID_RESET_PASSWORD_TOKEN)
    {
    }

    private function respondWithMessage(string $string)
    {
    }

}
