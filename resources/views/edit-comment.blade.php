@extends('layout')

@section('content')
    <div style="display: flex; align-items: center; margin-left: auto; margin-right: auto;    width: 80%">


        <div class="mb-3" style="width: 100%">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{route('comments.update', $comment->id)}}" method="POST">
                @method('PUT')
                @csrf
                <textarea style="margin-top: 10px" class="form-control" id="exampleFormControlTextarea1" rows="3"
                          placeholder="Text"
                          name="body" >{{$comment->body}}</textarea>
                <button style="margin: 10px" type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>

    </div>

@endsection

