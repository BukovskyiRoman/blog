@extends('layout')

@section('content')
    <div
        style="display: flex; align-items: center; margin-left: auto; margin-right: auto; width: 65%; position: relative">

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
            <div class="mb-3" style="width: 100%">
                <form action="{{route('posts.store')}}" method="POST">
                    @csrf
                    <input style="margin-top: 10px" class="form-control form-control-lg" type="text" placeholder="Title"
                           aria-label=".form-control-lg example" name="title">
                    <textarea style="margin-top: 10px" class="form-control" id="textareaEditor" rows="3" name="body">{{$loremIpsumText}} </textarea>

                    <button style="margin: 10px" type="submit" class="btn btn-primary">Add post</button>
                    <button style="margin: 10px" onclick="clearBox('textareaEditor')" class="btn btn-primary" type="button">Clear</button>
                </form>
            </div>
        @endauth
    </div>

    <div style="align-items: center; margin-left: auto; margin-right: auto; width: 65%; ">
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

        @foreach($posts as $post)


            <div
                style="border: solid 1px cadetblue; border-radius: 5px; margin-top: 10px; padding: 10px; position: relative">
                <a href="{{route('posts.show', $post->id)}}"><h4>Title: {{$post->title}}</h4></a>

                <a style="text-decoration: none" href="/posts/?author={{$post->author->id}}"><h5>
                        Author: {{$post->author->name}}</h5></a>
                <h6>Date: {{ $post->created_at->diffForHumans() }}</h6>
                <p>{{\Illuminate\Support\Str::limit($post->body), 70}}</p>

                @csrf
                <button style="margin: 0.5%; position:relative;" type="submit" class="btn btn-primary btn-like"
                        id="{{$post->id}}" onClick="reply_click(this.id)">Like post
                    | {{count($post->like)}}</button>

                @auth()
                    <form style="position: relative; margin-left: 10%; margin-top: -4.6%" method="POST"
                          action="{{route('posts.destroy', $post->id)}}">
                        @csrf
                        @method('DELETE')
                        <button style="margin: 10px" type="submit" class="btn btn-primary">Delete</button>
                    </form>

                    <form style="position: relative; margin-left: 17.5%; margin-top: -6.3%" method=""
                          action="{{route('posts.edit', $post->id)}}">
                        @csrf
                        <button style="margin: 10px" type="submit" class="btn btn-primary">Edit</button>
                    </form>
                @endauth

                <div>
                    <h6 style="margin: 10px">Comments:</h6>
                    @foreach($post['comments'] as $comment)

                        <div style="margin: 2px; padding: 5px; border-radius: 5px; border: solid 1px lightskyblue">
                            {{$comment->body}}
                        </div>
                        <div style="position: relative; padding: 0">
                            <button style="margin-top: 0.5%; margin-left: 0.2%; position: relative" type="submit"
                                    class="btn btn-primary btn btn-like-comment" id="{{$comment->id}}">Like
                                | {{count($comment->like)}}</button>
                            <form style="position: absolute; margin-left: 12%; margin-top: -2.65%" method="POST"
                                  action="{{route('comments.destroy', $comment->id)}}">
                                @csrf
                                @method('DELETE')
                                <button style="margin-top: -4.3%; margin-left: -30%; width: 10em; display: block"
                                        type="submit"
                                        class="btn btn-primary">Delete
                                </button>
                            </form>

                            <form method="POST" style="margin-left: 22.6%; margin-top: -3.3%; padding: 0; position:relative; display: block;
"
                                  action="{{route('comments.edit', $comment->id)}}">
                                @csrf
                                @method('GET')
                                <button style="margin: 0; width: 10em" type="submit" class="btn btn-primary">Edit
                                </button>
                            </form>
                        </div>
                    @endforeach

                    <form method="POST" style="margin-left: 2%; display: block; margin-top: 1%"
                          action="{{route('comments.store', $post->id)}}">
                        @csrf
                        <textarea style="margin-top: 10px" class="form-control" rows="3"
                                  placeholder="Add comment" name="comment"></textarea>
                        <button style="margin: 10px" type="submit" class="btn btn-primary" id="{{$post->id}}">Add comment</button>
                        <textarea style="visibility: hidden" name="id">{{$post->id}}</textarea>
                    </form>
                </div>

            </div>
        @endforeach

        {{--            {{dd($posts->links())}}--}}
        <div class="container" style="margin: 50px auto; padding-left: 30%">
            {{ $posts->links() }}
        </div>
    </div>
@endsection

