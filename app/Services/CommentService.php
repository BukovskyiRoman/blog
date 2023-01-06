<?php

namespace App\Services;

use App\Jobs\ProcessCommentsIndex;
use App\Models\Comment;
use App\Services\Interfaces\CommentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentService implements CommentServiceInterface
{
    public function update(Request $request, Comment $comment)
    {
        $comment->body = $request->get('body');
        $comment->save();
        ProcessCommentsIndex::dispatch();
    }

    public function create(Request $request)
    {
        $ownerData = $this->getCommentOwner($request);
        $comment = Comment::create([
            $ownerData['userCategory'] => $ownerData['userId'],
            'body' => $request->get('comment'),
            'post_id' => $request->get('id')
        ]);
        ProcessCommentsIndex::dispatch();
        return $comment;
    }

    private function getCommentOwner(Request $request)
    {
        if ($request->hasCookie('guest')) {
            $visitorId = $request->cookies->get('guest');
        }

        Auth::check() ? $userCategory = 'user_id' : $userCategory = 'visitor_id';
        Auth::check() ? $id = Auth::user()->id : $id = $visitorId;
        return [
            'userCategory' => $userCategory,
            'userId' => $id
        ];
    }

    public function destroy(Comment $comment): bool
    {
        $success = false;
        if ($comment->isAuthor()) {
            $comment->deleteOrFail();
            ProcessCommentsIndex::dispatch();
            $success = true;
        }
        return $success;
    }
}
