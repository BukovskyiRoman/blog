<?php

namespace App\Services\Interfaces;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

interface PostServiceInterface
{
    public function destroy(Post $post);
}
