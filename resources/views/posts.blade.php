@extends('layout')

@section('content')
{{--    @auth()--}}
{{--    <aside class="w-1/6 absolute ml-6" aria-label="Sidebar">--}}
{{--        <div class="px-3 py-4 overflow-y-auto rounded bg-gray-50 dark:bg-gray-800 border-1">--}}
{{--            @foreach($posts as $post)--}}
{{--                <h6>{{ $post->title }}</h6>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </aside>--}}
{{--    @endauth--}}

    <div class="flex align-items-center mr-auto mr-auto w-full relative">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @auth()
            <div class="w-3/5 mr-auto ml-auto align-items-center">
                <form action="{{route('posts.store')}}" method="POST">
                    @csrf
                    <input style="margin-top: 10px" class="form-control form-control-lg" type="text" placeholder="Title"
                           aria-label=".form-control-lg example" name="title">
                    <textarea style="margin-top: 10px" class="form-control" id="textArea" rows="7"
                              name="body">{{$loremIpsumText}} </textarea>

                    <button style="margin: 10px" type="submit" class="btn btn-primary">Add post</button>
                    <button style="margin: 10px" onclick="clearBox('textArea')" class="btn btn-primary" type="button">
                        Clear
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <div class="w-3/5 mr-auto ml-auto align-items-center">
        @if(session()->has('register'))
            <div x-data="{show:true}"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 style="position: fixed; background: #a0aec04a; color: #2563eb; margin-left: 27%; border-radius: 5px; padding: 10px">
                <p style="margin-top: 7px">
                    {{ session('register') }}
                </p>
            </div>
        @endif
        {{--            {{dd($options)}}--}}
        <form action="{{route('posts.index')}}" method="GET">
            <select name="sort">
                <option value="desc" @if ($options['sort'] === 'desc') selected @endif> newest</option>
                <option value="asc" @if ($options['sort'] === 'asc') selected @endif>oldest</option>
                <option value="max" @if ($options['sort'] === 'max') selected @endif>like(max)</option>
                <option value="min" @if ($options['sort'] === 'min') selected @endif>like(min)</option>
            </select>
            <p><input type="submit" value="Sort"></p>
        </form>

        <form class="mt-3" method="GET" action="{{route('search.index')}}">
            @csrf
            <div class="mb-2">
                <input type="search" class="form-control" id="q" name="q" value={{ request()->get('q', '') }}>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        @foreach($posts as $post)
            <div
                style="border: solid 1px cadetblue; border-radius: 5px; margin-top: 10px; padding: 10px; position: relative">
                <a href="{{route('posts.show', $post->id)}}"><h4>Title: {{$post->title}}</h4></a>

                <a style="text-decoration: none" href="/posts/?author={{$post->author->id}}"><h5>
                        Author: {{$post->author->name}}</h5></a>
                <h6>Date: {{ $post->created_at->diffForHumans() }}</h6>
                <p>{{\Illuminate\Support\Str::limit($post->body), 70}}</p>

                <div class="flex pt-2">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-like mr-3"
                            id="{{$post->id}}" onClick="reply_click(this.id)">
                        Like post | {{count($post->like)}}
                    </button>

                    @auth()
                        <form method="POST"
                              action="{{route('posts.destroy', $post->id)}}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary mr-3">Delete</button>
                        </form>

                        <form method="POST"
                              action="{{route('posts.edit', $post->id)}}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Edit</button>
                        </form>
                    @endauth
                </div>

                <div class="container items-center justify-between mx-auto">
                    <h6 style="margin: 10px">Comments:</h6>
                    @foreach($post['comments'] as $comment)

                        <div class="border-2 p-2 ">
                            {{$comment->body}}
                        </div>
                        <div class="container flex flex-wrap items-center space-x-4 mx-auto p-2">
                            <button type="submit"
                                    class="btn btn-primary btn btn-like-comment" id="{{$comment->id}}">
                                &#9829; | {{count($comment->like)}}
                            </button>

                            <form method="POST"
                                  action="{{route('comments.destroy', $comment->id)}}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-primary">
                                    &#10060;
                                </button>
                            </form>

                            <form method="POST"
                                  action="{{route('comments.edit', $comment->id)}}">
                                @csrf
                                @method('GET')
                                <button type="submit" class="btn btn-primary">
                                    &#9999;
                                </button>
                            </form>
                        </div>
                    @endforeach

                    <form method="POST" style="margin-left: 2%; display: block; margin-top: 1%"
                          action="{{route('comments.store', $post->id)}}">
                        @csrf
                        <textarea style="margin-top: 10px" class="form-control" rows="3"
                                  placeholder="Add comment" name="comment"></textarea>
                        <button style="margin: 10px" type="submit" class="btn btn-primary" id="{{$post->id}}">Add
                            comment
                        </button>
                        <textarea style="visibility: hidden" name="id">{{$post->id}}</textarea>
                    </form>
                </div>

            </div>
        @endforeach

        <div class="container mt-3 mb-5">
            {{ $posts->links() }}
        </div>
    </div>
@endsection

