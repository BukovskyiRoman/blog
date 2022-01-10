<?php

namespace App\Repositories\Interfaces;

use http\Env\Request;

interface PostRepositoryInterface
{
    public function getPostByAuthor($author);
    public function getSortedPostByLikes($sort, $page, $perPage);
    public function getSortedPostsByDate($sort);

}
