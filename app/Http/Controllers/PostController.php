<?php

namespace App\Http\Controllers;

use App\Events\AddNewPost;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Jobs\ProcessBlog;
use App\Listeners\SendPostInfo;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use http\Cookie;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $options = $request->all(['sort']);

        $posts = Cache::remember('posts', 1, function () use ($request, $options) {                  //todo Cache в імені враховувати всі параметри адресного рядка
            $sort = 'desc';
            $posts = Post::query();

            if ($request->has('author')) {
                $posts = $posts->where('user_id', $request->get('author'));
            }

            if ($request->has('sort')) {
                if ((strcmp('max', $request->get('sort')) == 0) || (strcmp('min', $request->get('sort')) == 0)) {
                    if (strcmp('min', $request->get('sort')) == 0) {
                        $sort = 'asc';
                    }
                    $page = $request->has('page') ? $request->get('page') : 1;
                    $perPage = 5;
                    $posts_number = Post::query()->count();

                    $result = DB::table('posts AS p')                                              //todo !!!!!!!!!!!!!!!!!!!!!!!!!!
                    ->leftJoin('likes as l', 'p.id', 'l.post_id')
                        ->select(['p.id', DB::raw('count(distinct l.id) AS post_likes')])
                        ->groupBy('p.id')
                        ->orderBy('post_likes', $sort)
                        ->take($perPage)->skip(($page - 1) * $perPage)
                        ->get()->map(function ($item) {
                            return Post::where('id', $item->id)->first();
                        });
                    return (new LengthAwarePaginator($result, $posts_number, $perPage, $page))              //todo !!!!!!!!!!!!!
                    ->withPath(route('posts.index'))
                        ->appends($options);
                } else {
                    $sort = $request->get('sort');
                }
            }
            return $posts->orderBy('created_at', $sort)
                ->with('comments', 'author', 'like')
                ->simplePaginate(5);
        });

        if (\auth()->check()) {
            return view('posts', compact('posts', 'options'));
        }

        if ($request->hasCookie('guest')) {
            $id = $request->cookies->get('guest');
        } else {
            $id = DB::table('visitors')->max('visitor_id');

            if ($id === null) {
                $id = 1;
                DB::table('visitors')->updateOrInsert([
                    'visitor_id' => $id,
                ]);
            } else {
                $id++;
                DB::table('visitors')->updateOrInsert([
                    'visitor_id' => $id,
                ]);
            }
        }

        $cookie = cookie('guest', $id);
        return response()->view('posts', ['posts' => $posts, 'options' => $options])->withCookie($cookie);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title' => $validatedData['title'],
            'body' => $validatedData['body']
        ]);

        // event(new AddNewPost($post));
        ProcessBlog::dispatch($post);
        return redirect(route('posts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //$post = Post::where('id', '=', $id)->with('like', 'comments', 'commentLikes')->first(); //todo !!!!
        $post = Post::findOrFail($id);
        $postLikes = Like::where('post_id', $post->id)->count();
        $comments = Comment::where('post_id', $post->id)->get();
        $comment_ids = $comments->map(fn($item) => $item->id);
        $commentLikes = DB::table('likes')
            ->whereIn('comment_id', $comment_ids)
            ->select(['comment_id', DB::raw('count(*) as number_of_likes')])
            ->groupBy('comment_id')->get()->reduce(function ($carry, $item) {
                $carry[$item->comment_id] = $item->number_of_likes;
                return $carry;
            }, []);
        //dd($commentLikes);
        return view('one-post', compact('post', 'postLikes', 'comments', 'commentLikes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function edit(Post $post)
    {
        if (auth()->user()->is_admin === 1 || \auth()->check() && $post->user_id === auth()->user()->id) {
            return view('post-edit', compact('post'));
        }
        return redirect('/posts')->withErrors("You can't edit this post");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();
        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Post $post)
    {
        if (auth()->user()->is_admin === 1 || \auth()->check() && $post->user_id === auth()->user()->id) {
            $post->delete();
            return redirect('/posts');
        }
        return redirect('/posts')->withErrors("You can't delete this post");
    }

}
