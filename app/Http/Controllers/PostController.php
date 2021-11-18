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

        $category = $request->get('category');
        if (isset($category)) {
            $condition['category'] = $category;
        }

        $search = $request->get('search');
        if (isset($search)) {
            $condition[] = ['content', 'like' , "%$search%"];
        }

        $startDate = $request->get('startdate');
        $endDate = $request->get('enddate');

        $q = Post::query()->where($condition);

        if (isset($startDate, $endDate)) {
            $q = $q->whereBetween('published_at', [$startDate, $endDate]);
        }

        $posts = $q->get()->toArray();

        return response()->json($posts);
    }
}
