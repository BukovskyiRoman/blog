<?php

namespace App\Http\Controllers;

use App\Events\AddNewComment;
use App\Jobs\ProcessCommentsIndex;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use App\Services\Interfaces\CommentServiceInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    private CommentServiceInterface $commentService;

    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        throw new \Exception('Not implement');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $comment = $this->commentService->create($request);

        //event(new AddNewComment($comment));

        return redirect()->back();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Comment $comment
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit(Comment $comment)
    {
        return $comment->isAuthor() ?
            view('edit-comment', compact('comment')) :
            redirect()->back()->withErrors("You can't edit this comment");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Comment $comment
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request, Comment $comment)
    {
        $this->commentService->update($request, $comment);
        return redirect('/posts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function destroy(Comment $comment)
    {
        $status = $this->commentService->destroy($comment);
        return $status ? redirect()->back() : redirect()->back()->withErrors("You can't delete this comment");
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
