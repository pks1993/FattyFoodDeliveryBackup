<!DOCTYPE html>
        <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Laravel Google Maps Multiple Markers Example - ItSolutionStuff.com</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
            <style type="text/css">
                #map {
                  height: 450px;
                }
            </style>
        </head>

        <body>
            <div class="container mt-5">
                {{-- <h2>Laravel Google Maps Multiple Markers Example - ItSolutionStuff.com</h2> --}}
                <div id="map"></div>
                {{-- <p>@foreach($riders as $value)
                    <p>{{ $value->rider_id }}</p>
                @endforeach
                </p> --}}

                {{-- <input type="text" name="" id="data" value="{{ $data }}"> --}}
            </div>

            <script type="text/javascript">
                function initMap() {
                    const myLatLng = { lat: 22.9665, lng: 97.7525 };
                    const map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 13,
                        center: myLatLng,
                    });


                    // var locations =document.getElementById('data').value;
                    var locations=[
            ['Mumbai', 22.974384,97.761361],
            ['Pune', 22.936126,97.751064],
            ['Bhopal ', 22.964286,97.754655],
            ['Agra', 22.942919,97.75457],
            ['Delhi', 22.9444281,97.741002],
            ['Rajkot', 22.930166,97.751226],
        ];
                    // console.log(locations);

                    var infowindow = new google.maps.InfoWindow();

                    var marker, i;
                    var image='../../../../../image/rider.png'

                    for (i = 0; i < locations.length; i++) {
                          marker = new google.maps.Marker({
                            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                            map: map,
                            icon:image,

                          });

                          google.maps.event.addListener(marker, 'click', (function(marker, i) {
                            return function() {
                              infowindow.setContent(locations[i][0]);
                              infowindow.open(map, marker);
                            }
                          })(marker, i));

                    }
                }

                window.initMap = initMap;
            </script>

            <script type="text/javascript"
                src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap" ></script>

        </body>
        </html>
