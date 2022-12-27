<?php

namespace App\Repositories\Interfaces;

use App\Models\Post;
use http\Env\Request;

interface PostRepositoryInterface
{
    public function getPostByAuthor($author);
    public function getSortedPostByLikes($sort, $page, $perPage);
    public function getSortedPostsByDate($sort);
    public function createPost($request);
    public function updatePost(Post $post, $request);

}
