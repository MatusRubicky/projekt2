<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">

    <link rel="stylesheet" href="https://bootflat.github.io/bootflat/css/bootflat.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        #map {
            height: 100%;
            width: 100%;
            float: left;
        }
        
        .controls {
            margin-top: 10px;
            margin-right: 3px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 0px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        
        #pac-input {
            background-color: #fff;
            font-family: Avenir;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 300px;
        }
        
        #pac-input:focus {
            border-color: #4d90fe;
        }
        
        .pac-container {
            font-family: Avenir;
        }
        
        #type-selector {
            color: #fff;
            background-color: #4d90fe;
            padding: 5px 11px 0px 11px;
        }
        
        #type-selector label {
            font-family: Avenir;
            font-size: 13px;
            font-weight: 300;
        }
        
        #right-panel {
            overflow-y: scroll;
            float: right;
            width: 31%;
            height: 100%;
        }
        
        #right-panel {
            font-family: 'Arial', 'Avenir';
            line-height: 30px;
        }
        
        #right-panel select,
        #right-panel input {
            font-size: 12px;
        }
        
        #right-panel select {
            width: 100%;
        }
        
        #right-panel i {
            font-size: 11px;
        }
        
        #target {
            width: 70px;
        }
        
        .width {
            width: 100px;
        }
        
        .panel {
            height: 100%;
            overflow: auto;
        }
        
        .right {
            float: right;
        }
        
        .no_shadow {
            box-shadow: 0 0 0 0;
        }
        
        .input {
            width: 100%;
        }
        
        .close {
            background: url('https://cdn0.iconfinder.com/data/icons/slim-square-icons-basics/100/basics-22-128.png') no-repeat 5px center;
            background-size: contain;
        }
        
        .navigate {
            background: url('https://cdn4.iconfinder.com/data/icons/iconsimple-freebies/512/compass_2.png') no-repeat center center;
            background-size: contain;
        }
        
        .equalButton {
            padding: 10px;
        }
        
        .white {
            fill: #000;
        }
    </style>
    <title>Map of shops</title>
</head>

<body>
    <div id="map"></div>
    <input id="pac-input" class="controls" type="text" placeholder="Search">
    <input id="navigate" class="navigate no_shadow equalButton controls white" type="button">
    <div id="right-panel">
        <div>
            <input id="closeBtn" class="target controls no_shadow close equalButton" type="button">
        </div>
        <div id="label">
            <input id="way-input" class="controls input" type="text" placeholder="Start position">
        </div>
        <input id="wayTo-input" class="controls input" type="text" placeholder="End position">
    </div>


    <script>
        var pos;
        var destination;
        var map;
        var startPos;
        var geocoder;
        var markers = [];
        var windows = [];

        function init() {
            geocoder = new google.maps.Geocoder();
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 48.803245536,
                    lng: 19.742431641
                },
                zoom: 3,
                mapTypeId: google.maps.MapTypeId.SATELLITE
            });
            ////
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer({
                draggable: true,
                map: map,
                panel: document.getElementById('right-panel')
            });

            ////
            // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            var navigate = document.getElementById('navigate');
            var closeBtn = document.getElementById('closeBtn');
            var searchBox = new google.maps.places.SearchBox(input);
            var sidePanel = document.getElementById('right-panel');
            var panelOrigin = document.getElementById('way-input');
            var panelOriginSearchBox = new google.maps.places.SearchBox(panelOrigin);
            var panelDes = document.getElementById('wayTo-input');
            var panelSearchBox = new google.maps.places.SearchBox(panelDes);

            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(navigate);

            closeBtn.addEventListener("click", function () {
                document.getElementById("map").style.width = "100%";
                document.getElementById("closeBtn").style.display = "none";
                document.getElementById("right-panel").style.display = "none";
            });
            document.getElementById("closeBtn").style.display = "none";
            document.getElementById("right-panel").style.display = "none";
            searchBox.addListener('places_changed', function () {
                searchPlaces(searchBox);
            });
            panelSearchBox.addListener('places_changed', function () {
                searchPlaces(panelSearchBox);
                najdiCestu("navigateBox");
            });
            panelOriginSearchBox.addListener('places_changed', function () {
                searchPlaces(panelSearchBox);
                najdiCestu("navigateBox");
            });
            navigate.addEventListener("click", function () {
                najdiCestu();
            });

            function searchPlaces(box) {
                var places = box.getPlaces();
                if (places.length != 0) {
                    destination = places[0].name;
                    // Clear out the old markers.
                    markers.forEach(function (marker) {
                        marker.setMap(null);
                    });

                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function (place) {

                        // Create a marker for each place.
                        markers.push(new google.maps.Marker({
                            map: map,
                            title: place.name,
                            position: place.geometry.location
                        }));

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                } else {
                    alert("Enter valid destination address");
                }
            }

            window.onload = function () { //get user permition to geolocate

                var geoOptions = {
                    timeout: 5 * 1000
                }
                getPosts();

                var geoSuccess = function (position) {
                    startPos = position;
                    console.log(startPos);
                    pos = {
                        lat: startPos.coords.latitude,
                        lng: startPos.coords.longitude
                    };
                    geocoder.geocode({
                        'location': pos
                    }, function (results, status) {
                        startPos = results[0];
                    });
                    map.setCenter({
                        lat: startPos.coords.latitude,
                        lng: startPos.coords.longitude
                    }); //set the loc if userpermition=T
                    map.setZoom(12);
                };
                var geoError = function (error) {
                    console.log('Error occurred. Error code: ' + error.code);
                    // error.code can be:
                    //   0: unknown error
                    //   1: permission denied
                    //   2: position unavailable (error response from location provider)
                    //   3: timed out
                };

                navigator.geolocation.getCurrentPosition(geoSuccess, geoError, geoOptions);
            };

            function displayRoute(origin, destination, service, display, box) {
                document.getElementById("map").style.width = "69%";
                document.getElementById("right-panel").style.display = "block"
                document.getElementById("closeBtn").style.display = "inline";

                if (startPos === undefined) {
                    alert("Unable to get your current location")
                }

                if (destination != undefined && startPos != undefined) {
                    if (box == "panel") {
                        var places = panelOriginSearchBox.getPlaces();
                        origin = places[0].formatted_address;
                    } else {
                        console.log(destination)
                        origin = startPos.formatted_address;
                    }
                    service.route({
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING,
                        avoidTolls: true
                    }, function (response, status) {
                        if (status === google.maps.DirectionsStatus.OK) {
                            display.setDirections(response);

                        } else {
                            alert('Could not display directions due to: ' + status);
                        }
                    });

                    panelOrigin.value = origin;
                    panelDes.value = destination;
                }
                if (destination === undefined) {
                    panelDes.value = "Enter valid destination address";
                }
            }

            function najdiCestu(box) {
                if (box == "navigateBox") {
                    var places = panelSearchBox.getPlaces();
                    destination = places[0].formatted_address;
                    displayRoute(pos, destination, directionsService,
                        directionsDisplay, "panel");
                } else {
                    displayRoute(pos, destination, directionsService,
                        directionsDisplay, "");
                }
            }
        }

        function show(json) {
            /*  json = parsed = [{
                  "id": "7",
                  "name": "Alza.sk",
                  "coord": "48.72398560",
                  "coord2": "21.25784770"
              }, {
                  "id": "10",
                  "name": "Exisport",
                  "coord": "48.72397560",
                  "coord2": "21.22775770"
              }, {
                  "id": "8",
                  "name": "Datart.sk",
                  "coord": "48.72598560",
                  "coord2": "21.23774870"
              }, {
                  "id": "11",
                  "name": "Mtbiker.sk",
                  "coord": "48.72197560",
                  "coord2": "21.25974970"
              }]; */
            var parsed = JSON.parse(JSON.stringify(json));
            var shops = [];

            for (var x in parsed) {
                shops.push(parsed[x]);
            }

            shops.forEach(function (windplace) {
                var position = {
                    lat: parseFloat(windplace.coord),
                    lng: parseFloat(windplace.coord2)
                }

                var marker = new google.maps.Marker({
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                    position: position,
                    clickable: true,
                    map: map,
                    animation: google.maps.Animation.DROP
                });

                google.maps.event.addListener(marker, 'click', function () {
                    var infowindow = new google.maps.InfoWindow();
                    var infolist = jQuery('<ul></ul>');
                    for (attribute in windplace) {
                        infolist.append('<li><b>' + attribute + '</b>: ' + windplace[attribute] + '</li>');
                    }
                    infowindow.setContent('<div class="infowindow">' + infolist.html() + '</div>');
                    windows.forEach(function (window) {
                        window.close();
                    });

                    infowindow.open(map, marker);
                    windows.push(infowindow);
                    map.panTo(marker.getPosition());
                });
            });
        }

        function getPosts() {
            $.getJSON("db.php", function (json) {
                show(json);
            });
            // show(); //off upjs
        };
    </script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <?php
	require_once 'keyValue.php';
	echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . $key . '&libraries=places&callback=init" async defer></script>' ;
?>
</body>

</html>