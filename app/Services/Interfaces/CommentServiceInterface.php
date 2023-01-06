<?php

namespace App\Services\Interfaces;

use App\Models\Comment;
use Illuminate\Http\Request;

interface CommentServiceInterface
{
    public function update(Request $request, Comment $comment);
    public function create(Request $request);
    public function destroy(Comment $comment);
}
