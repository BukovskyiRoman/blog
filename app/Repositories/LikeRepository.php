<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use http\Env\Request;
use Illuminate\Support\Facades\DB;

class LikeRepository implements LikeRepositoryInterface
{
    public function getLikesByPostId($postId)
    {
        return Like::where('post_id', $postId)->get();
    }
}
