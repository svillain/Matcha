var msg_datas = null;
var current = null;

function scrollToBottom() {
    var history = $(".chat-history");
    var h = history.get(0).scrollHeight;
    history.animate({scrollTop: h});
    $("#currentChat").show();
}

function drawList() {
    $("#matchList").empty();
    if (msg_datas !== undefined && msg_datas !== null) {
        $.each(msg_datas, function (id, target) {
            var status = "offline (" + moment(target.user.data.disconnected_at, "YYYY-MM-DD").fromNow() + ")";
            if (target.user.data.online === 1)
                status = "online";
            $("#matchList").append(
                $('<li class="clearfix" style="cursor: pointer;">' +
                    '<img height="50" width="50" src="' + target.photo + '" alt="avatar"/>' +
                    '<div class="about">' +
                    '<div class="name">' + target.user.data.first_name + ' ' + target.user.data.last_name + '</div>' +
                    '<div class="status">' +
                    '<i class="fa fa-circle ' + status + '"></i> ' + status +
                    '</div>' +
                    '</div>' +
                    '</li>'
                ).click(function (e) {
                    e.preventDefault();
                    if (target.user.data.id !== current)
                        drawOne(target.user.data.id);

                }));
        });
    }
}

function drawOne(id) {
    current = id;
    var obj = msg_datas[id];
    if (obj !== undefined && obj !== null) {

        $("#chat-history").empty();
        $("#chat-img").attr('src', obj.photo);

        $("#chat-with").html('<a href="/profil/' + id + '" target="_blank">' + obj.user.data.first_name + ' ' + obj.user.data.last_name);
        $.each(obj.messages, function (key, msg) {
            var name = (parseInt(msg.data.user_id) === parseInt(id)) ? obj.user.data.first_name : "Me";
            var position = (parseInt(msg.data.user_id) === parseInt(id)) ? "left" : "right";
            var type = (parseInt(msg.data.user_id) === parseInt(id)) ? "other" : "my";
            var content = msg.data.content;
            var circle = (parseInt(msg.data.user_id) === parseInt(id)) ? "me" : "online";
            var date = msg.data.created_at;

            $("#chat-history").append(
                $('<li class="clearfix">' +
                    '<div class="message-data align-' + position + '">' +
                    '<span class="message-data-time">' + date + '</span> &nbsp; &nbsp;' +
                    '<span class="message-data-name">' + name + '</span> <i class="fa fa-circle ' + circle + '"></i>' +
                    '</div>' +
                    '<div class="message ' + type + '-message float-' + position + '">' + content + '</div>' +
                    '</li>')
            );
        });
    } else {
        document.location.reload(true);
    }
}

// MESSAGE EVENT ON CLICK
$("#message-button").click(function (e) {
    var append = "";
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '/me/chat/send',
        data: {target: current, content: $("#message-to-send").val()},
        dataType: 'json',
        error: function () {
            console.log('Bad request');
        },
        success: function (data) {
            append = $('<li class="clearfix">' +
                '<div class="message-data align-left">' +
                '<span class="message-data-time">' + data.message.data.created_at + '</span> &nbsp; &nbsp;' +
                '<span class="message-data-name">Me</span> <i class="fa fa-circle me"></i>' +
                '</div>' +
                '<div class="message my-message float-left">' + data.message.data.content + '</div>' +
                '</li>');
        },
        complete: function () {
            $("#chat-history").append(append);
            $("#message-to-send").val('');
            scrollToBottom();
        }
    });
});


// MESSAGE ON ENTER TRY TO SEND
$("#message-to-send").keypress(function (e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code === 13) {
        $("#message-button").trigger('click');
        return true;
    }
});

// AJAX INIT CHAT
$.ajax({
    type: 'POST',
    url: '/me/chat/update',
    dataType: 'json',
    success: function (data) {
        if (data) {
            msg_datas = data;
            drawList();
            drawOne(Object.keys(msg_datas)[0]);
            scrollToBottom();
            setInterval(function () {
                $.ajax({
                    type: 'POST',
                    url: '/me/chat/update',
                    data: {},
                    dataType: 'json',
                    error: function () {
                        console.log('Bad request lul');
                    },
                    success: function (data) {
                        if (data) {
                            var old = msg_datas;
                            msg_datas = data;
                            drawList();
                            drawOne(current, old);
                        }
                        else {
                            $("#currentChat").hide();
                        }
                    },
                });
            }, 1500);
        }
        else {
            $("#currentChat").hide();
        }
    },
    error: function (e, t, v) {
    }
});