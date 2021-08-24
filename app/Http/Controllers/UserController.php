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
//use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\JsonResponse;
class UserController extends Controller
{
    public function register(Request $request): JsonResponse
    {
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


    public function login(Request $request): JsonResponse
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

    public function refreshToken(Request $request): JsonResponse
    {

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = Route::dispatch($proxy);

        $response = json_decode($response->getContent());


        return response()->json([
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' => $response]);
    }

    public function forgetPassword(Request $request): JsonResponse
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $response = Password::sendResetLink($input);

        $message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : 'SOMETHING_WRONG';
        if ($message== 'SOMETHING_WRONG'){
            $data = [
                'status' => false,
                'statusCode' => 400,
                'message' => $message,
                'items' => '',

            ];
        }
        if ($message== 'Mail send successfully'){
            $data = [
                'status' => true,
                'statusCode' => 200,
                'message' => $message,
                'items' => '',

            ];
        }


        return response()->json($data);
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

    /*  public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        return response()->json(["message" => 'If an account with the corresponding e-mail exists a password reset link will be send to the provided address.'], 200);
    }*/


    public function getUser($user_id = null): JsonResponse
    {
        if (isset($user_id)) {
            $user = User::find($user_id);
        } else
            $user = auth()->user();

        $data=[
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' => $user
        ];
return response()->json($data);
    }



    public function editUser(Request $request): JsonResponse
    {
        $user = Auth::user();
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

    public function addFriend(Request $request): JsonResponse
    {

        $myFriend_ids = friends::where('user_id',\auth()->user()->id)->pluck('friend_id')->toArray();
        $id=$request->input('friend_id');
        if(in_array($id,$myFriend_ids)){

            $data = [
                'status' => true,
                'statusCode' => 200,
                'message' => 'Error',
                'item'=>  'friend is exists',

            ];

            return response()->json($data);
        }


        $friend= Friends::create([
            'friend_id'=>$request->input('friend_id'),
            'user_id'=>Auth::user()->id,

        ]);

        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'friend is add successfully',
            'item'=>  $friend,

        ];

        return response()->json($data);
    }


    public function friendlist(): JsonResponse
    {

        $friends=Friends::where('user_id',\Auth::user()->id)->get();
        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' => $friends,

        ];
        return response()->json($data);
    }
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'items' => 'Successfully logged out',
        ];
        return response()->json($data);
    }

}
