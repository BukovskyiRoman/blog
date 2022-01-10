<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use http\Env\Request;
use Illuminate\Support\Facades\DB;

class PostRepository implements PostRepositoryInterface
{
    public function getPostByAuthor($author)
    {
        return Post::where('user_id', $author)->paginate(5)->withQueryString();
    }

    public function getSortedPostByLikes($sort, $page, $perPage)
    {
        return DB::table('posts AS p')                                              //todo !!!!!!!!!!!!!!!!!!!!!!!!!!
        ->leftJoin('likes as l', 'p.id', 'l.post_id')
            ->select(['p.id', DB::raw('count(distinct l.id) AS post_likes')])
            ->groupBy('p.id')
            ->orderBy('post_likes', $sort)
            ->take($perPage)->skip(($page - 1) * $perPage)
            ->get()->map(function ($item) {
                return Post::where('id', $item->id)->first();
            });
    }

    public function getSortedPostsByDate($sort)
    {
        return Post::orderBy('created_at', $sort)
            ->with('comments', 'author', 'like')
            ->paginate(5)
            ->withQueryString();
    }
}
