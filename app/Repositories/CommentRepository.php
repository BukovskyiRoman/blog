<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use http\Env\Request;
use Illuminate\Support\Facades\DB;

class CommentRepository implements CommentRepositoryInterface
{
    public function getCommentLikes($postId)
    {
        $comments = $this->getCommentByPostId($postId);
        $comment_ids = $comments->map(fn($item) => $item->id);
        return Like::whereIn('comment_id', $comment_ids)
            ->select(['comment_id', DB::raw('count(*) as number_of_likes')])
            ->groupBy('comment_id')->get()->reduce(function ($carry, $item) {
                $carry[$item->comment_id] = $item->number_of_likes;
                return $carry;
            }, []);
    }

    public function getCommentByPostId($postId)
    {
        return Comment::where('post_id', $postId)->get();
    }
}
