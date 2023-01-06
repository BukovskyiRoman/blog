@extends('layout')

@section('content')
    <div class="flex align-items-center mr-auto mr-auto w-full relative">
        <div class="w-3/5 mr-auto ml-auto align-items-center">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{route('posts.update', $post->id)}}" method="POST">
                @method('PUT')
                @csrf
                <input style="margin-top: 10px" class="form-control form-control-lg" type="text" placeholder="Title"
                       aria-label=".form-control-lg example" name="title" value="{{$post->title}}">
                <textarea style="margin-top: 10px" class="form-control" id="exampleFormControlTextarea1" rows="15"
                          placeholder="Text"
                          name="body" >{{$post->body}}</textarea>
                <button type="submit" class="btn btn-primary mt-2">Save</button>
            </form>
        </div>

    </div>

@endsection
