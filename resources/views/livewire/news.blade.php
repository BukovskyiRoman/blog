@extends('layout')

@section('content')
    <div style="align-items: center; margin-left: 26%; margin-right: auto;  width: 46%">
        @foreach($news as $post)
            <div
                style="border: solid 1px cadetblue; border-radius: 5px; margin-top: 10px; padding: 10px; position: relative">
                <h4>Title: {{$post->title}}</h4>
                <h5>Author: {{$post->author->name}}</h5>
                <h6>Date: {{ $post->created_at }}</h6>
                <p>{{ $post->body }}</p>

                <a class="text-indigo-600 hover:text-yellow-400">Edit</a>
                <a class="text-red-600 hover:text-green-900-800">Delete</a>
            </div>
        @endforeach

        <div class="container" style="margin: 50px auto; padding-left: 30%">
            {{ $news->links() }}
        </div>
    </div>
@endsection
