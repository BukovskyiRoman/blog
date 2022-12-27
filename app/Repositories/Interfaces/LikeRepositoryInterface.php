<?php

namespace App\Repositories\Interfaces;

interface LikeRepositoryInterface
{
    public function getLikesByPostId($postId);

}
