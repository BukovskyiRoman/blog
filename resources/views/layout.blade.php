<!DOCTYPE HTML>
<head>
    <script src="{{URL::asset('js/app.js')}}"></script>

    <link href="https://vjs.zencdn.net/7.17.0/video-js.css" rel="stylesheet" />

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
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>

    <script type="text/javascript">
        tinymce.init({
            selector: '#textareaEditor',
            width: 917,
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
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>

<nav class="bg-gray-200 border-gray-200 px-2 sm:px-4 py-2.5 rounded dark:bg-gray-900 w-3/5 ml-auto mr-auto">
    <div class="container flex flex-wrap items-center justify-between mx-auto">
        <a href="/posts" class="flex items-center">
            <img src="https://flowbite.com/docs/images/logo.svg" class="h-6 mr-3 sm:h-9" alt="Flowbite Logo" />
            <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">Blog</span>
        </a>
        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <ul class="flex flex-col p-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                <li>
                    <a href="/news">News</a>
                </li>
                <li>
                    <a href="@if(auth()->check() && auth()->user()->is_admin) {{route('admin.profile')}} @else {{route('profile')}} @endif">Profile</a>
                </li>
                <li>
                    <a href="/login">Login</a>
                </li>
                <li>
                    <a href="/register">Registration</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

@auth()
    <aside class="w-1/6 absolute ml-6" aria-label="Sidebar">
        <div class="px-3 py-4 overflow-y-auto rounded bg-gray-50 dark:bg-gray-800 border-1">
            @widget('UsersWidget')
        </div>
    </aside>
@endauth

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


    let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function(html) {
            let switchery = new Switchery(html,  { size: 'small' });
        });


    $(document).ready(function(){                           //change user status on moderator
        $('.js-switch').change(function () {
            let status = $(this).prop('checked') === true ? 1 : 0;
            let userId = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('change-status') }}',
                data: {'status': status, 'user_id': userId},
                success: function (data) {
                    toastr.options.closeButton = true;        // add success message about status change
                    toastr.options.closeMethod = 'fadeOut';
                    toastr.options.closeDuration = 100;
                    toastr.success(data.message);
                }
            });
        });
    });


</script>
