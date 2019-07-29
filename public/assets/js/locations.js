function createMap(lng, lat) {
    mapboxgl.accessToken = 'pk.eyJ1IjoiYWRlbGhvbSIsImEiOiJjamt6azllaDgwYXVtM3BucWxiczhjNmFwIn0.pdTZQA8UavzvF2duz3wvSQ';
    var map = new mapboxgl.Map({
        container: 'map', // container id
        style: 'mapbox://styles/adelhom/cjlaxsvis4xvq2soc80aiiia1', // stylesheet location
        center: [lng, lat], // starting position [lng, lat]
        zoom: 5 // starting zoom
    });
    map.on('load', function () {
        map.resize(new Event('resize'));
        var nav = new mapboxgl.NavigationControl();
        var scale = new mapboxgl.ScaleControl();
        map.addControl(nav, 'top-left');
        map.addControl(scale, 'top-right');
        map.loadImage('/assets/img/marker.png', function (error, image) {
            map.addImage('marker', image);
        });
        updateMap(this, lng, lat);
    });
    return map;
}

function updateMap(map, lng, lat) {
    var layer = {
        "id": "places",
        "type": "symbol",
        "source": {
            "type": "geojson",
            "data": {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "geometry": {
                        "type": "Point",
                        "coordinates": [lng, lat]
                    }
                }]
            },
        },
        "layout": {
            "icon-image": "marker",
            "icon-size": 1
        }
    };
    if (lng !== '' && lat !== '') {
        getLocationDetails(lat, lng);
    }
    if (map.getLayer('places'))
        map.removeLayer('places');
    if (map.getSource('places'))
        map.removeSource("places");
    map.addLayer(layer);
    map.flyTo({center: layer.source.data.features[0].geometry.coordinates});
    $("input[name*='longitude']").val(lng);
    $("input[name*='latitude']").val(lat);
}

function getLocationDetails(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);
    var storableLocation = [];
    new google.maps.Geocoder().geocode({'latLng': latlng}, function (results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            for (var ac = 0; ac < results[0].address_components.length; ac++) {
                var component = results[0].address_components[ac];

                if (component.types.includes('sublocality') || component.types.includes('locality')) {
                    storableLocation.city = component.long_name;
                }
                else if (component.types.includes('administrative_area_level_1')) {
                    storableLocation.state = component.short_name;
                }
                else if (component.types.includes('country')) {
                    storableLocation.country = component.long_name;
                    storableLocation.registered_country_iso_code = component.short_name;
                }
            }
            var details = $('#mapDetails');
            details.empty();

            if (storableLocation.registered_country_iso_code !== undefined)
                details.append("<img width='30' src='/assets/img/flags/" + storableLocation.registered_country_iso_code.toLowerCase() + ".png'>");
            if (storableLocation.country !== undefined)
                details.append(" " + storableLocation.country);
            if (storableLocation.state !== undefined)
                details.append(", " + storableLocation.state);
            if (storableLocation.city !== undefined)
                details.append(", " + storableLocation.city);
        }
    });
}

function getCoordinates(map) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };
            updateMap(map, pos.lng, pos.lat);
        }, function () {
            updateMapWithIp(map);
        });
    } else {
        alert("Browser doesn't support Geolocation");
    }
}

function initCoord(callback) {
    var pos = {
        lat: $("input[name*='latitude']").val(),
        lng: $("input[name*='longitude']").val()
    };

    if ($("input[name*='latitude']").val() === undefined || $("input[name*='longitude']").val() === undefined || $("input[name*='latitude']").val() === null || $("input[name*='longitude']").val() === null ||$("input[name*='latitude']").val().length === 0 || $("input[name*='longitude']").val().length === 0) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                callback(pos);
            }, function () {
                $.get("http://ip-api.com/json", function (response) {
                    var lat = response.lat;
                    var lng = response.lon;
                    var pos = {
                        lat: lat,
                        lng: lng
                    };

                    callback(pos);
                }, "jsonp");
            });
        } else {
            alert("Browser doesn't support Geolocation");
        }
    }
    else
        callback(pos);
}

function updateMapWithIp(map) {
    $.get("http://ip-api.com/json", function (response) {
        var lat = response.lat;
        var lng = response.lon;
        pos = {
            lat: lat,
            lng: lng
        };
        updateMap(map, lng, lat);
    }, "jsonp");
}