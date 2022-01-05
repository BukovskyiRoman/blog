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
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\PostRepository;
use App\Services\LoremIpsumService;
use http\Cookie;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\Routing\Route;

class PostController extends Controller
{
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->authorizeResource(Post::class, 'post');
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request, LoremIpsumService $loremIpsum)
    {
        $loremIpsumText = $loremIpsum->getLoremIpsumText();
        $options = $request->all(['sort']);

        $sort = 'desc';
        //$posts = Post::query();

        if ($request->has('author')) {
            $posts = $this->postRepository->getPostByAuthor($request->get('author'));
            return view('posts', compact('posts', 'loremIpsumText', 'options'));
        }

        if ($request->has('sort')) {
            if ((strcmp('max', $request->get('sort')) == 0) || (strcmp('min', $request->get('sort')) == 0)) {
                if (strcmp('min', $request->get('sort')) == 0) {
                    $sort = 'asc';
                }
                $page = $request->has('page') ? $request->get('page') : 1;
                $perPage = 5;
                $posts_number = Post::query()->count();

                $result = $this->postRepository->getSortedPostByLikes($sort, $page, $perPage);

                $posts = (new LengthAwarePaginator($result, $posts_number, $perPage, $page))              //todo !!!!!!!!!!!!!
                ->withPath(route('posts.index'))
                    ->appends($options);
                return view('posts', compact('posts', 'loremIpsumText', 'options'));
            } else {
                $sort = $request->get('sort');
            }
        }
        $posts = $this->postRepository->getSortedPostsByDate($sort);

        if (\auth()->check()) {
            return view('posts', compact('posts', 'options', 'loremIpsumText'));
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
        return response()->view('posts', compact('posts', 'options', 'loremIpsumText'))->withCookie($cookie);
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

        event(new AddNewPost($post));
        //Artisan::call('post:process 1 --queue');
        ProcessBlog::dispatch($post);
        return redirect(route('posts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function show($id)
    {
//        $post = cache()->remember("post$id", now()->addDay(), function () use ($id) {  //todo Cache в імені враховувати всі параметри адресного рядка
//            var_dump("cache post$id");
//            return Post::findOrFail($id);
//        });
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
//        if (auth()->user()->is_admin || \auth()->check() && $post->user_id === auth()->user()->id) {
//            return view('post-edit', compact('post'));
//        }

        return view('post-edit', compact('post'));
        //return redirect('/posts')->withErrors("You can't edit this post");
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

        cache()->put("post$post->id", $post);

        return redirect('/posts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Post $post)
    {
//        if (! Gate::allows('delete-post', $post)) {
//            //return Response::deny('Action denied');
//            return redirect('/posts')->withErrors("You can't delete this post");
//        }

        $post->delete();
        return redirect('/posts');
    }
}
