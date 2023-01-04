<?php

namespace App\Http\Controllers;

use App\Events\AddNewPost;
use App\Http\Requests\StorePostRequest;
use App\Jobs\ProcessBlog;
use App\Jobs\ProcessPostIndex;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Notifications\TelegramNotification;
use App\Repositories\CommentRepository;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\LikeRepository;
use App\Repositories\PostRepository;
use App\Services\CookieService;
use App\Services\Interfaces\CookieServiceInterface;
use App\Services\LoremIpsumService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Elasticsearch;

class PostController extends Controller
{
    private PostRepositoryInterface $postRepository;
    private LikeRepositoryInterface $likeRepository;
    private CommentRepositoryInterface $commentRepository;
    private CookieServiceInterface $cookieService;

    public function __construct(
        PostRepositoryInterface $postRepository,
        LikeRepositoryInterface $likeRepository,
        CommentRepositoryInterface $commentRepository,
        CookieServiceInterface $cookieService,
    )
    {
        //$this->authorizeResource(Post::class, 'post');
        $this->postRepository = $postRepository;
        $this->likeRepository = $likeRepository;
        $this->commentRepository = $commentRepository;
        $this->cookieService = $cookieService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Http\Response
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
                $page = $request->get('page', 1);
                $perPage = config('settings.pagination.posts');
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

        $guestId = $this->cookieService->setGuestCookie($request);
        $cookie = cookie('guest', $guestId);
        return response()->view('posts', compact('posts', 'options', 'loremIpsumText'))->withCookie($cookie);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(StorePostRequest $request)
    {
        $post = $this->postRepository->createPost($request);
        $post->notify(new TelegramNotification());
        event(new AddNewPost($post));
        //Artisan::call('post:process 1 --queue');

        ProcessBlog::dispatch($post);
        ProcessPostIndex::dispatch();
        return redirect(route('posts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function show($id)
    {
//        $post = cache()->remember("post$id", now()->addDay(), function () use ($id) {  //todo Cache в імені враховувати всі параметри адресного рядка
//            var_dump("cache post$id");
//            return Post::findOrFail($id);
//        });
        $post = $this->postRepository->getPostById($id);
        $postLikes = $this->likeRepository->getLikesByPostId($post->id)->count();
        $comments = $this->commentRepository->getCommentByPostId($post->id);
        $commentLikes = $this->commentRepository->getCommentLikes($post->id);

        return view('one-post', compact('post', 'postLikes', 'comments', 'commentLikes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function edit(Post $post)
    {
//        if (auth()->user()->is_admin || \auth()->check() && $post->user_id === auth()->user()->id) {
//            return view('post-edit', compact('post'));
//        }

        return view('post-edit', compact('post'));
    }

    /**
     * Method for post updating
     * @param StorePostRequest $request
     * @param Post $post
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $post = $this->postRepository->updatePost($post, $request);
        cache()->put("post$post->id", $post);
        ProcessPostIndex::dispatch();
        return redirect('/posts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Post $post)
    {
//        if (! Gate::allows('delete-post', $post)) {
//            //return Response::deny('Action denied');
//            return redirect('/posts')->withErrors("You can't delete this post");
//        }

        $post->delete();
        ProcessPostIndex::dispatch();
        return redirect('/posts');
    }
}
