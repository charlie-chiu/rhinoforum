<?php

namespace App\Http\Controllers;

use App\Repositories\PostRepository;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPosts(Request $request)
    {
        $posts = $this->postRepository->get(
            $request->get('uid'),
            $request->get('category'),
            $request->get('search'),
            $request->get('startdate'),
            $request->get('enddate'),
        );

        return response()->json($posts);
    }
}
