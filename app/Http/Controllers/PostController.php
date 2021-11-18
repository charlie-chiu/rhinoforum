<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function getPosts(Request $request)
    {
        $condition = [];

        $userID = $request->get('uid');
        if (isset($userID)) {
            $condition['user_id'] = $userID;
        }

        $posts = Post::query()->where($condition)->get()->toArray();

        return response()->json($posts);
    }
}
