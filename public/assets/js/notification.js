// AJAX INIT CHAT

var notify_datas = null;

function deleteOne(li, not) {
    $.ajax({
        type: 'POST',
        url: '/me/notification/read',
        data: {
            id: not.data.id
        },
        dataType: 'json',
        error: function () {
            console.log("Bad request");
        },
        success: function (data) {
            li.remove();
        },
    });
}


function drawNotifications() {
    if (notify_datas && notify_datas !== undefined) {
        $("#notifications-list").empty();
        $.each(notify_datas, function (id, not) {
            $("#notifications-list").append(
                $('<li class="notification" style="cursor: pointer">' +
                    '<p class="notification-time">' + not.data.updated_at + '</p>' +
                    '<p class="notification-message">' + not.data.message + '</p>' +
                    '</li>'
                ).click(function (e) {
                    e.preventDefault();
                    deleteOne(this, not);
                }));
        });
    }
}

function notify() {
    $.ajax({
        type: 'POST',
        url: '/me/notification/update',
        data: {},
        dataType: 'json',
        error: function () {
            console.log("Bad request");
        },
        success: function (data) {
            notify_datas = data;
            drawNotifications();
            setInterval(function () {
                if ($('#notifications-list li').length === 0)
                    $('#notificationsButton').removeClass("animated bounce infinite");
                else
                    $('#notificationsButton').addClass("animated bounce infinite");
                $.ajax({
                    type: 'POST',
                    url: '/me/notification/update',
                    data: {},
                    dataType: 'json',
                    error: function () {
                        console.log("Bad request");
                    },
                    success: function (data) {
                        if (data) {

                            notify_datas = data;
                            drawNotifications();
                        }
                    },
                });
            }, 1500);
        },
    });
}