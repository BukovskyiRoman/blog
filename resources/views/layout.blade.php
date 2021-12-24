<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script defer src="https://unpkg.com/alpinejs@3.2.4/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        var pusher = new Pusher('2d98b5d868d66f6639a7', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('test');
        channel.bind('App\\Events\\AddNewPost', function (data) {
            document.location.reload();
        });

        const page_load_time = parseInt((new Date().getTime() / 1000).toFixed(0));


        window.addEventListener('load', (event) => {
            const handler = function () {
                console.log("this is the message");
                $.ajax({
                    type: 'GET',
                    url: "/comments/info?ts=" + page_load_time,
                    // data: {},
                    success: function (data) {
                        console.log(data);
                        if (data.is_modified) {
                            document.location.reload();
                        } else {
                            setTimeout(handler, 7000);
                        }
                    }
                });

            };
            setTimeout(handler, 7000);
        });

    </script>
</head>

<div style="display: flex; align-items: center; margin-left: auto; margin-right: auto;    width: 65%">
    <a style="text-decoration: none; margin-left: 15px" href="/posts"><h1>Blog</h1></a>
    <a href="{{route('profile')}}" style="font-size: 30px; text-decoration: none; display: flex; margin-left: 57%">Profile</a>
    <a href="/login" style="font-size: 30px; text-decoration: none; display: flex; margin-left: 2%">Login</a>
    <a href="/register" style="font-size: 30px; text-decoration: none; display: flex; margin-left: 2%">Registration</a>
</div>

@yield('content')


<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".btn-like").click(function (e) {
        e.preventDefault();
        const post_id = e.target.id;
        const user_id = {{ auth()->user() ? auth()->user()->getAuthIdentifier() : request()->cookies->get('guest') }};

        $.ajax({
            type: 'POST',
            url: "/like/post",
            data: {post_id: post_id, user_id: user_id},
            success: function (data) {
                alert(data.success);
            }
        });
    });

    $(".btn-like-comment").click(function (e) {
        e.preventDefault();
        const comment_id = e.target.id;
        const user_id = {{ auth()->user() ? auth()->user()->getAuthIdentifier() : request()->cookies->get('guest') }};

        $.ajax({
            type: 'POST',
            url: "/like/comment",
            data: {comment_id: comment_id, user_id: user_id},
            success: function (data) {
                alert(user_id);
            }
        });
    });
</script>
