<?php

namespace App\Http\Controllers;
use App\ApiCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Models\Friends;

class UserController extends Controller
{
    public function register(Request $request){
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation'=>'required|max:1000'
        ]);

        $validateData['password']=bcrypt($request->password);
        $user= User::create($validateData);



        $accessToken= $user->createToken('authToken')->accessToken; ///authToken تم تسمية الtoken باي اسم

        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' =>[
                'user'=>$user,
                'access_Token'=>$accessToken,
                ],
        ];
        return response()->json($data);
    }


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
    public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        return response()->json(["message" => 'If an account with the corresponding e-mail exists a password reset link will be send to the provided address.'], 200);
    }

    public function reset(ResetPasswordRequest $request) {
        $reset_password_status = Password::reset($request->validated(), function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return $this->respondBadRequest(ApiCode::INVALID_RESET_PASSWORD_TOKEN);
        }

        return $this->respondWithMessage("Password has been successfully changed");
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        //$user = User::find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->input('password'));
        $user->save();
        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' =>$user,

        ];
        return response()->json($data);
    }

    public function addFriend(Request $request){
        $requestData['friend_id']=$request->friend_id;
        $requestData['user_id']=Auth::user()->id;

        $friend= Friends::create($requestData);


        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'friend is add successfully',
            'item'=>  $friend,

        ];

        return response()->json($data);
    }
    public function friendlist()
    {
        $user_id =  Auth::user()->id;
        $friends=Friends::where('user_id',"like","$user_id")->get();
        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' => $friends,

        ];
        return response()->json($data);
    }

}
