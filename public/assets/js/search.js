var sliderAge = document.getElementById('sliderAge');
var sliderLocation = document.getElementById('sliderLocation');
var sliderPopularity = document.getElementById('sliderPopularity');

var order = $("#orderBy");
var suggestionMode = $("#suggestionMode");
var userInfo = {};

var start = false;
var end = false;

var request = {
    orderBy: order.children(':selected').attr('data-id'),
    limits: [0, 28],
    filters: {
        age: [0, 0],
        location: [0, 0],
        popularity: [0, 0],
        interests: ['']
    }
};

Array.prototype.isArray = true;

order.change(function () {
    request.orderBy = order.children(':selected').attr('data-id');
});

suggestionMode.click(function (e) {
    e.preventDefault();
    sliderAge.noUiSlider.set([userInfo.age - 5, userInfo.age + 5]);
    sliderPopularity.noUiSlider.set([0, 100]);
    sliderLocation.noUiSlider.set([0, 50]);
    request.filters.popularity = [0, 100];
    request.filters.location = [0, 50];
    $("#interests").val(userInfo['interests'].map(x => x['id'])).trigger('chosen:updated');

    request.filters.interests = $("#interests").val();





    /*($('#interests option')).each(function (index, value) {
    })*/
    /*request.filters.age = [userInfo.age - 5, userInfo.age + 5];
    $(userInfo.interests).each(function (index, value) {

    });*/

});

function sendRequest() {

    $.ajax({
        type: 'POST',
        url: '/me/search',
        data: {request},
        dataType: 'json',
        error: function () {
            console.log("Bad request");
        },
        success: function (data) {
            if (!(data['result'].length === 0)) {
                console.clear();
                end = false;
                console.log(data['sql']);
                console.log(request);
                console.table(data['result'].map(x => x['data']));
                console.table(data['raw']);
                drawUsersFind(data['result'].map(x => x['data']));
            }
            else {
                if (!end) {
                    console.log("Search end, no more result");
                    var html = $('#find-users');
                    if (request.limits[0] === 0)
                        html.append("<div class='col-12 text-center' align='center'><h5>No user match</h5></div>");
                    else
                        html.append("<div class='col-12 text-center' align='center'><h5>No more users my friend</h5></div>")
                    end = true;
                    start = false;
                }
            }
        },
    });
}

function infiniteScroll() {
    var deviceAgent = navigator.userAgent.toLowerCase();
    var agentID = deviceAgent.match(/(iphone|ipod|ipad)/);


    $(window).scroll(function () {
        if (($(window).scrollTop() + $(window).height()) === $(document).height()
            || agentID && ($(window).scrollTop() + $(window).height()) + 150 > $(document).height()) {
            if (start) {
                request.limits[0] += 28;
                request.limits[1] += 28;
                sendRequest();

            }
        }
    });
}

function drawUsersFind(users) {
    function _calculateAge(birthday) {
        var bd = new Date(birthday);
        var ageDifMs = Date.now() - bd.getTime();
        var ageDate = new Date(ageDifMs); // miliseconds from epoch
        return Math.abs(ageDate.getUTCFullYear() - 1970);
    }

    var html = $('#find-users');
    if (users === undefined || !users.isArray) {
        html.append("<p>No users find!</p>");
        return;
    }

    var append = "";
    $(users).each(function (index, user) {
        var icon_sexe = (user.sexe_id === 1) ? 'mars' : 'venus';
        var path_profil = "";

        $(user.photos.map(x => x['data'])).each(function (index2, photo) {
            if (photo.profil === 1) {
                path_profil = 'upload/' + photo.fileName;
            }
        });


        append += "<div class=\"col-lg-3 col-md-4 col-sm-6 col-xs-12 text-center p-1\" style=\"\">\n" +
            "    <div class=\"p-1 img-block-hover\" style=\"border: 1px solid #6441BC;\">\n" +
            "        <p>" + user.first_name + " " + user.last_name + " (" + _calculateAge(user.birthdate) + " <i class=\"fa fa-" + icon_sexe + " text-primary\"></i>)</p>\n" +
            "        <img src=\"/" + path_profil + "\"\n" +
            "             class=\"img-hover rounded img-fluid\" width=\"128\" height=\"128\"\n" +
            "             style=\"min-height: 128px; min-width: 128px; max-height: 128px;max-width: 128px;\">\n" +
            "        <div class=\"img-hover-text\">\n" +
            "            <a target=\"_blank\" href=\"/profil/" + user.id + "\" class=\"btn btn-primary\"><i class=\"material-icons\">visibility</i></a>\n" +
            "        </div>\n" +
            "    </div>\n" +
            "</div>";
    });

    html.append(append);
}

$("#search-apply").click(function (e) {
    e.preventDefault();
    $('#find-users').empty();
    start = true;
    end = false;
    request.limits = [0, 28];
    sendRequest();
});

function updateSlider() {
    sliderAge.noUiSlider.on('update.one', function (value, handle, unencoded, tap, positions) {
        $("#i-age").text("[" + value[0] + ", " + value[1] + "]");
    });
    sliderLocation.noUiSlider.on('update.one', function (value, handle, unencoded, tap, positions) {
        $("#i-location").text("[" + value[0] + ", " + value[1] + "]");
    });
    sliderPopularity.noUiSlider.on('update.one', function (value, handle, unencoded, tap, positions) {
        $("#i-popularity").text("[" + value[0] + ", " + value[1] + "]");
    });
}

function changeSlider() {
    sliderAge.noUiSlider.on('update.one', function (value, handle, unencoded, tap, positions) {
        request.filters.age = value;
    });
    sliderLocation.noUiSlider.on('change.one', function (value, handle, unencoded, tap, positions) {
        request.filters.location = value;
    });
    sliderPopularity.noUiSlider.on('change.one', function (value, handle, unencoded, tap, positions) {
        request.filters.popularity = value;
    });
    $('#interests').on('change', function () {
        request.filters.interests = ($(this).val());
        request.filters.interests.push('');
    });
}

function searchInit(uInfo) {
    userInfo = uInfo;
    noUiSlider.create(sliderAge, {
        start: [0, 0],
        connect: true,
        range: {
            min: 0,
            max: 100
        },
        format: {
            from: function (value) {
                return parseInt(value);
            },
            to: function (value) {
                return parseInt(value);
            }
        }
    });

    noUiSlider.create(sliderLocation, {
        start: [0, 0],
        connect: true,
        range: {
            min: 0,
            max: 100
        },
        format: {
            from: function (value) {
                return parseInt(value);
            },
            to: function (value) {
                return parseInt(value);
            }
        }
    });

    noUiSlider.create(sliderPopularity, {
        start: [0, 0],
        connect: true,
        range: {
            min: 0,
            max: 100
        },
        format: {
            from: function (value) {
                return parseInt(value);
            },
            to: function (value) {
                return parseInt(value);
            }
        }
    });

    var config = {
        '.chosen-select': {},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-rtl': {rtl: true},
        '.chosen-select-width': {width: '95%'}
    };
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    // Listener on change
    updateSlider();
    changeSlider();
    infiniteScroll();
}