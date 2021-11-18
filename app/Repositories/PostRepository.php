<?php

namespace App\Repositories;
use App\Models\Post;

class PostRepository
{
    public function get(?int $userID, ?string $category, $keyword, $startDate, $endDate): array
    {
        $condition = [];

        if (isset($userID)) {
            $condition['user_id'] = $userID;
        }

        if (isset($category)) {
            $condition['category'] = $category;
        }

        if (isset($keyword)) {
            $condition[] = ['content', 'like' , "%$keyword%"];
        }

        $q = Post::query()->where($condition);

        if (isset($startDate, $endDate)) {
            $q = $q->whereBetween('published_at', [$startDate, $endDate]);
        }

        return $q->get()->toArray();
    }
}
