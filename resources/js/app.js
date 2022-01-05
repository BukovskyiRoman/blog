require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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
        $.ajax({
            type: 'GET',
            url: "/comments/info?ts=" + page_load_time,
            success: function (data) {
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
