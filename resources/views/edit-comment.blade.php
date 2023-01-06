@extends('layout')

@section('content')
    <div class="flex align-items-center mr-auto mr-auto w-full relative">
        <div class="w-3/5 mr-auto ml-auto align-items-center" >

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
                <textarea class="form-control mt-3 p-2"
                          id="exampleFormControlTextarea1"
                          rows="3"
                          placeholder="Text"
                          name="body">{{$comment->body}}
                </textarea>
                <button type="submit" class="btn btn-primary mt-2">Save</button>
            </form>
        </div>

    </div>

@endsection

