<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function get(?int $userID, ?string $category, $keyword, $startDate, $endDate, ?int $limit, $page): array
    {
        $q = Post::query();

        if (isset($userID)) {
            $q = $q->where('user_id', $userID);
        }

        if (isset($category)) {
            $q = $q->where('category', $category);
        }

        if (isset($keyword)) {
            $q = $q->where('content', 'like', "%$keyword%");
        }

        if (isset($startDate, $endDate)) {
            $q = $q->whereBetween('published_at', [$startDate, $endDate]);
        }

        if (isset($limit)) {
            if (isset($page)) {
                return $q->paginate($limit, ['*'], 'page', $page)->items();
            } else {
                return $q->paginate($limit)->items();
            }
        }

        return $q->get()->toArray();
    }
}
