<!DOCTYPE HTML>
<head>
    <script src="{{URL::asset('js/app.js')}}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script defer src="https://unpkg.com/alpinejs@3.2.4/dist/cdn.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.tiny.cloud/1/j47r7hh3zgfukjuzev2q82rzeafkr56kbwi00ud49mxhvdhh/tinymce/5/tinymce.min.js"
            referrerpolicy="origin">
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>

    <script type="text/javascript">
        tinymce.init({
            selector: '#textareaEditor',
            width: 1190,
            height: 300,
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'table emoticons template paste help'
            ],
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent |' +
                'forecolor backcolor emoticons | help',

            menubar: 'favs file edit view format tools table help',
        });
    </script>

    <script>


        function clearBox(elementID)
        {
            document.getElementById(elementID).innerHTML = "";
        }
    </script>
</head>

<div style="display: flex; align-items: center; margin-left: auto; margin-right: auto;    width: 65%">
    <a style="text-decoration: none; margin-left: 15px" href="/posts"><h1>Blog</h1></a>
    <a href="@if(auth()->check() && auth()->user()->is_admin === 1) {{route('admin.profile')}} @else {{route('profile')}} @endif"
       style="font-size: 30px; text-decoration: none; display: flex; margin-left: 57%">Profile</a>
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
