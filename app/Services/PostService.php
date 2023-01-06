<?php

namespace App\Services;

use App\Jobs\ProcessCommentsIndex;
use App\Jobs\ProcessPostIndex;
use App\Models\Comment;
use App\Models\Post;
use App\Services\Interfaces\CommentServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostService implements PostServiceInterface
{
    public function destroy(Post $post)
    {
        if ($post->isAuthor()) {
            $post->deleteOrFail();
            ProcessPostIndex::dispatch();
            return redirect()->route('posts.index');
        }
        return redirect()->route('posts.index')->withErrors('You can not delete this post');
    }
}
