<?php


namespace App\Http\Controllers;
use App\ApiCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Models\Posts;
use App\Models\Favourites;
use App\Models\Likes;
use App\Models\Comments;
use App\Models\Friends;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{

    public function create(Request $request): JsonResponse
    {
        $post = Posts::create([
            'text' => $request->input('text'),
            'user_id' => Auth::user()->id,
        ]);
        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Post has been added',
            'items' => $post,

        ];

        return response()->json($data);

    }




    public function editPost($post_id,Request $request): JsonResponse
    {

        $post = Posts::find($post_id);

        if (isset($post)) {
            if(Auth::user()->id==$post->user_id) {
                $post->text = $request->text;
                $post->save();
                $data = [
                    'status' => true,
                    'statusCode' => 200,
                    'message' => 'Success',
                    'items' => $post,

                ];

                return response()->json($data);
            }
            $data = [
                'status' => true,
                'statusCode' => 400,
                'message' => 'error',
                'items' =>'not authrise',

            ];

            return response()->json($data);


        }

        $data = [
            'status' => false,
            'statusCode' => 422,
            'message' => 'Error',
            'items' => 'their is now post',

        ];

        return response()->json($data);
    }
    public function deletePost($post_id): JsonResponse
    {

        $post = posts::find($post_id);

        if (isset($post)) {
            if(Auth::user()->id==$post->user_id) {
                $comments= Comments::where("post_id","like","%$post_id%")->get();
              $post->delete();

                $comments->delete();
                $data = [
                    'status' => true,
                    'statusCode' => 200,
                    'message' => 'Success',
                    'items' => 'post deleted ',

                ];

                return response()->json($data);
            }
            $data = [
                'status' => true,
                'statusCode' => 400,
                'message' => 'error',
                'items' =>'not authrise',

            ];

            return response()->json($data);


        }

        $data = [
            'status' => false,
            'statusCode' => 422,
            'message' => 'Error',
            'items' => 'their is now comment',

        ];

        return response()->json($data);
    }




    public function show($post_id): JsonResponse
    {
        $post = Posts::find($post_id);
        if (isset($post)) {
            $data = [
                'status' => true,
                'statusCode' => 200,
                'message' => 'Success',
                'items' => $post,

            ];

            return response()->json($data);

        }

        $data = [
            'status' => false,
            'statusCode' => 422,
            'message' => 'Error',
            'items' => 'their is now post',

        ];

        return response()->json($data);

    }

    public function favourite(Request $request): JsonResponse
    {
        $request=$request->input('post_id');
        if(!$request==Posts::find($request)){
            $data = [
                'status' => false,
                'statusCode' => 422,
                'message' => 'There is no Post has this id',
                'items' => '',

            ];

            return response()->json($data);
        }
        $favourites = Favourites::create([
            'post_id' => $request,
            'user_id' => Auth::user()->id,
        ]);

        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Post has been added to your favorites',
            'items' => $favourites,

        ];

        return response()->json($data);
    }


    public function like(Request $request): JsonResponse
    {
        $request= $request->input('post_id');
        $likes= Likes::where("post_id","like","%$request%")->first();
        if(!$request==Posts::find($request)){

            $data = [
                'status' => false,
                'statusCode' => 422,
                'message' => 'There is no Post has this id',
                'items' => '',

            ];

            return response()->json($data);
        }elseif (isset($likes)){
            //$likes = likes::find($id);
            $likes->delete();

            $data = [
                'status' => false,
                'statusCode' => 422,
                'message' => 'Post has been delete like',
                'items' => '',

            ];
            return response()->json($data);
        }

        $likes = likes::create([
            'post_id' => $request,
            'user_id' => Auth::user()->id,
        ]);

        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'Post has been like',
            'items' => $likes,

        ];

        return response()->json($data);
    }


    public function comment(Request $request): JsonResponse
    {
        $text=$request->input('text');
        $request=$request->input('post_id');

        if(!$request==Posts::find($request)){
            $data = [
                'status' => false,
                'statusCode' => 422,
                'message' => 'There is no Post has this id',
                'items' => '',

            ];

            return response()->json($data);
        }
        $comments = Comments::create([
            'post_id' => $request,
            'text' => $text,
            'user_id' => Auth::user()->id,
        ]);

        $data = [
            'status' => true,
            'statusCode' => 200,
            'message' => 'comment success',
            'items' => $comments,

        ];

        return response()->json($data);
    }

    public function editComment($comment_id,Request $request): JsonResponse
    {

        $comment = Comments::find($comment_id);

        if (isset($comment)) {
            if(Auth::user()->id==$comment->user_id) {
                $comment->text = $request->input('text');
                $comment->save();
                $data = [
                    'status' => true,
                    'statusCode' => 200,
                    'message' => 'Success',
                    'items' => $comment,

                ];

                return response()->json($data);
            }
            $data = [
                'status' => true,
                'statusCode' => 400,
                'message' => 'error',
                'items' =>'not authrise',

            ];

            return response()->json($data);


        }

        $data = [
            'status' => false,
            'statusCode' => 422,
            'message' => 'Error',
            'items' => 'their is now comment',

        ];

        return response()->json($data);
    }

    public function deleteComment($comment_id): JsonResponse
    {

        $comment = Comments::find($comment_id);

        if (isset($comment)) {
            if(Auth::user()->id==$comment->user_id) {

                $comment->delete();
                $data = [
                    'status' => true,
                    'statusCode' => 200,
                    'message' => 'Success',
                    'items' => 'comment deleted ',

                ];

                return response()->json($data);
            }
            $data = [
                'status' => true,
                'statusCode' => 400,
                'message' => 'error',
                'items' =>'not authrise',

            ];

            return response()->json($data);


        }

        $data = [
            'status' => false,
            'statusCode' => 422,
            'message' => 'Error',
            'items' => 'their is now comment',

        ];

        return response()->json($data);
    }

    public function showComment($post_id): JsonResponse
    {
        $post = Posts::find($post_id);
        if (isset($post)) {
            $comments= Comments::where("post_id","like","%$post_id%")->get();
            $data = [
                'status' => true,
                'statusCode' => 200,
                'message' => 'Success',
                'items' => ['The Post',$post,'There Comments',$comments],

            ];

            return response()->json($data);

        }

        $data = [
            'status' => false,
            'statusCode' => 422,
            'message' => 'Error',
            'items' => 'their is now post',

        ];

        return response()->json($data);

    }

    /*------------------function timeLine() show user and his friends posts===========================*/

    public function timeLine(): JsonResponse
    {

        $my_post_ids = Posts::where('user_id',\auth()->user()->id)->pluck('id')->toArray();

        $myFriend_ids = friends::where('user_id',\auth()->user()->id)->pluck('friend_id')->toArray();
        $friend_post_ids = Posts::whereIn('user_id',$myFriend_ids)->pluck('id')->toArray();

        $page_record = \request()->get('page_record') ?? 10;
        $current_page_num = \request()->get('current_page_num') ?? 1;

        $posts_id = array_unique(array_merge($my_post_ids,$friend_post_ids));

        $objects = Posts::whereIn('id',$posts_id);

        $total_records = $objects->count();
        $total_pages = ceil($total_records/$page_record);
        $posts = $objects->skip($page_record*($current_page_num-1))->take($page_record)->get();

        $data = [
            'status'=>true,
            'statusCode'=>200,
            'message'=>'Success!',
            'items'=>[
                'data'=>$posts,
                'total_records'=>$total_records,
                'current_page_num'=>intval($current_page_num),
                'total_pages'=>$total_pages,
            ],
        ];

        return response()->json($data);
    }

}
