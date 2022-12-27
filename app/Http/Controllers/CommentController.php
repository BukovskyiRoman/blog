<?php

namespace App\Http\Controllers;

use App\Events\AddNewComment;
use App\Models\Comment;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        throw new \Exception('Not implement');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->hasCookie('guest')) {
            $visitorId = $request->cookies->get('guest');
        }

        Auth::check() ? $userId = 'user_id' : $userId = 'visitor_id';
        Auth::check() ? $id = Auth::user()->id : $id = $visitorId;

        $comment = Comment::create([
            $userId => $id,
            'body' => $request->comment,
            'post_id' => $request->id
        ]);

        //event(new AddNewComment($comment));

        return redirect()->back();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        if ($comment->user_id == Auth::user()->id) {
            return view('edit-comment', compact('comment'));
        }
        return redirect('posts')->withErrors("You can't edit this comment");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Comment $comment)
    {
        //dd($request);
        $comment->body = $request->body;
        $comment->save();
        return redirect('/posts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Comment $comment)
    {
        if ($comment->isAuthor()) {
            $comment->deleteOrFail();
            return redirect('/posts');
        }
        return redirect('/posts')->withErrors("You can't delete this comment!!!");
    }


    /**
     * @param Request $request
     * @return bool[]
     */
    public function checkAddComment(Request $request)
    {
        $request->validate([
            'ts' => ['required', 'integer', 'min:1636899738'],
        ]);
        $timestamp = Carbon::createFromTimestamp($request->get('ts'));                                           //todo Carbon
        $comments_last_write_time = Cache::remember('comments-last-write-time', 86400 * 30, function () {
            return Carbon::now();
        });
        return ['is_modified' => ($comments_last_write_time >= $timestamp)];
    }
}
