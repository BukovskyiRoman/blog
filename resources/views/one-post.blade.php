@extends('layout')

@section('content')
    <div style="align-items: center; margin-left: auto; margin-right: auto; width: 65%; ">
        <div style="border: solid 1px cadetblue; border-radius: 5px; margin-top: 10px; padding: 10px">
            <h4>Title: {{$post->title}}</h4>
            <h6>Date:{{$post->created_at}}</h6>
            <h6>Author: <a href=""> {{$post->author->name}}</a></h6>
            <p>{{$post->body}}</p>

            @auth()
            <form method="POST" action="{{route('posts.destroy', $post->id)}}">
                @method('DELETE')
                @csrf
                <button style="margin: 10px" type="submit" class="btn btn-primary ">Delete</button>
            </form>
            <form method="POST" action="{{route('posts.edit', $post->id)}}">
                @method('GET')
                @csrf
                <button style="margin: 10px" type="submit" class="btn btn-primary ">Edit</button>
            </form>
            @endauth

            <button style="margin: 10px" type="submit" class="btn btn-primary btn-like" onClick="reply_click(this.id)"
                    id="{{$post->id}}">Like post | {{$postLikes}}</button>

            <div>
                <h6 style="margin: 10px">Comments:</h6>
                @foreach($comments as $comment)
                    <div style="margin: 2px; border: solid 1px lightskyblue">
                        {{$comment->body}}
                    </div>

                    <button style="margin-left: 2%" type="submit" class="btn btn-primary btn-like-comment" id="{{$comment->id}}"
                            onClick="reply_click(this.id)">Like comment | {{ empty($commentLikes[$comment->id]) ? 0 : $commentLikes[$comment->id]}}</button>

                    <button style="margin-left: 5%" type="submit" class="btn btn-primary">Delete comment</button>
                    <button style="margin-left: 8%" type="submit" class="btn btn-primary">Edit comment</button>
                @endforeach

                <form method="POST" action="{{route('comments.store')}}">
                    @csrf
                    <textarea style="margin-top: 10px" class="form-control" id="exampleFormControlTextarea1" rows="3"
                              placeholder="Add comment"
                              name="comment"></textarea>
                    <button style="margin: 10px" type="submit" class="btn btn-primary">Add comment</button>
                    <textarea style="visibility: hidden" name="id">{{$post->id}}</textarea>
                </form>
            </div>

        </div>

    </div>


@endsection
