<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        dd('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->hasCookie('guest')) $visitorId = $request->cookies->get('guest');


        Auth::check() ? $userId = 'user_id' : $userId = 'visitor_id';
        Auth::check() ? $id = Auth::user()->id : $id = $visitorId;

        Comment::create([
            $userId => $id,
            'body' => $request->comment,
            'post_id' => $request->id
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('show');
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
        if (Auth::check()) {
            $check = $comment->user_id == Auth::user()->id;
        } else {
            $check = $comment->visitor_id == Cookie::get('guest');
        }

        if ($check) {
            $comment->deleteOrFail();
            return redirect('/posts');
        }
        return redirect('/posts')->withErrors("You can't delete this comment!!!");
    }
}
