@extends('layout')

@section('content')
    <div style="display: flex; align-items: center; margin-left: auto; margin-right: auto; width: 65%; position: relative; background-color: #cbd5e0;
  border-radius: 20px; padding: 20px; margin-top: 3%">
        <div style="position: relative; margin-left: 2%; margin-top: 0;  padding: 15px">
            <div style="display: flex; ">
                <h1>Personal cabinet</h1>
            </div>
            <div style="display: flex;">
                <div>
                    <h4>Name: {{$user->name}}</h4>
                    <h4>Email: {{$user->email}}</h4>
                </div>
            </div>
        </div>

        <div style="margin-left: 5%">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            <form method="POST" action="{{route('change.user.name')}}">
                @csrf
                <input type="text" id="inputPassword5" class="form-control" name="name"
                       value="{{$user->name}}">
                <button type="submit" class="btn btn-light" style="margin-top: 12px; width: 100%">Change name</button>
            </form>
        </div>
        <div style="margin-left: 5%; padding: 0">
            <form method="POST" style="padding: 0;" action="{{route('change.password')}}">
                @csrf
                <input type="password" class="form-control" aria-describedby="passwordHelpBlock"
                       placeholder="Old password" name="old_password">
                <input type="password" class="form-control" aria-describedby="passwordHelpBlock"
                       placeholder="New password" name="new_password">
                <input type="password" class="form-control" aria-describedby="passwordHelpBlock"
                       placeholder="Confirm new password" name="confirm_new_password">
                <button type="submit" class="btn btn-light" style="margin-top: 5px; margin-bottom: 2px; width: 100%">
                    Change password
                </button>
            </form>
        </div>
        <div style="display:flex; margin-left: 2%; height: 88px; margin-top: -3.35%">
            <a href="{{route('logout')}}">
                <button type="submit" class="btn btn-light" style="margin-top: 12px; height: 100%; width: 100%">LOGOUT
                </button>
            </a>
        </div>
    </div>

    <div style="display: flex; align-items: center; margin-left: auto; margin-right: auto; width: 65%; position: relative; background-color: #cbd5e0;
  border-radius: 10px; padding: 10px; margin-top: 2%">
        <div
            style="position: relative; margin-left: 3%; margin-top: 0;  padding: 15px; border-radius: 10px; border: solid #4a5568">
            <div style="height: 300px">

                @if($user->image === null)
                    <img style="margin-left: 15%; height: 280px; border-radius: 10px"
                         src="{{\Illuminate\Support\Facades\Storage::disk('local')->url('avatars/guest.jpeg')}}"
                         alt="alt text">
                @else
                    <img style="margin-left: 15%; height: 280px; border-radius: 10px"
                         src="{{\Illuminate\Support\Facades\Storage::disk('local')->url(''. $user->image->name)}}"
                         alt="alt text">
                @endif
            </div>
            <form style="margin: 0" method="POST" action="@if($user->image === null) {{route('images.store')}} @else {{route('images.update', 26)}} @endif" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    @if($user->image === null)
                        <label for="formFile" class="form-label">Download yor photo</label>
                        <input class="form-control" type="file" id="image" name="avatar">
                        <button type="submit" class="btn btn-light" style="margin-top: 12px; height: 100%; width: 100%">
                            Download
                        </button>
                    @else
                        @method('PUT')
                        <label for="formFile" class="form-label">Download yor photo</label>
                        <input class="form-control" type="file" id="image" name="avatar">
                        <button type="submit" class="btn btn-light" style="margin-top: 12px; height: 100%; width: 100%">
                            Edit
                        </button>
                    @endif
                </div>
            </form>


        </div>

        <div style="margin-left: 3%; border: solid #4a5568; border-radius: 10px; height: 29.7em; width: 45em;">
            <div style="margin: 0; padding-left: 12%; padding-top: 5%">
                <h3>Posts:</h3>
                @foreach($user->posts as $post)
                    <ul>
                        <li><a style="text-decoration: none; color: #2d3748; "
                               href="{{route('posts.show', $post->id)}}"> {{$post->title}}</a></li>
                    </ul>
                @endforeach
            </div>

        </div>


    </div>
@endsection
