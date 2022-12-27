<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface
{
    public function getCommentLikes($postId);
    public function getCommentByPostId($postId);
}
