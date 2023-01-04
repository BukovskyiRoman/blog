@extends('layout')

@section('content')
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

        <form class="mt-3" method="GET" action="{{route('search.index')}}">
            @csrf
            <div class="mb-2">
                <input type="search" class="form-control" id="q" name="q" value={{ request()->get('q', '') }}>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        @foreach($results as $result)
            <div
                style="border: solid 1px cadetblue; border-radius: 5px; margin-top: 10px; padding: 10px; position: relative">
                <a href="{{route('posts.show', $result->id)}}"><h4>Title: {{$result->title}}</h4></a>

{{--                <a style="text-decoration: none" href="/posts/?author={{$result->author->id}}"><h5>--}}
{{--                        Author: {{$result->author->name}}</h5></a>--}}
{{--                <h6>Date: {{ $result->created_at->diffForHumans() }}</h6>--}}
                <p>{{Str::limit($result->body), 70}}</p>

                @csrf
                <button style="margin: 0.5%; position:relative;" type="submit" class="btn btn-primary btn-like"
                        id="{{$result->id}}" onClick="reply_click(this.id)">Like post
                    | {{count($result->like)}}</button>
            </div>
        @endforeach

        <div class="container mt-3 mb-5">
            {{ $results->links() }}
        </div>
    </div>
@endsection

