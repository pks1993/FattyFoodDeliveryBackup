<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rider Location Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style type="text/css">
        #map {
          height: 100%;
        }
        html,
        body  {
            height: 100%;
            margin: 0;
            padding: 30px 10px 10px 10px;
        }
    </style>
</head>
<body>

<div id="map"></div>

<script type="text/javascript">
    function initMap() {
        const myLatLng = { lat: 22.9665, lng: 97.7525 };
        // var center_latitude=parseFloat('{{ $center_latitude }}').toFixed(10);
        // var center_longitude=parseFloat('{{ $center_longitude }}').toFixed(10);
        // const myLatLng ={ lat: center_latitude, lng: center_longitude };
        // console.log(myLatLng);
        const map = new google.maps.Map(document.getElementById("map"), {
            // zoom: 15,
            zoom: 7,
            center: myLatLng,
        });
        var locations = [
            @foreach ($riders as $data)
                // [ "restaurants/view/{{ $data->restaurant_id }}", "{{ $data->restaurant_name_en }}","{{ $data->restaurant_latitude }}", "{{ $data->restaurant_longitude }}" ],
                [ "riders/view/{{ $data->rider_id }}", "{{ $data->rider_user_name }}","{{ $data->rider_latitude }}", "{{ $data->rider_longitude }}" ],
            @endforeach
        ];

        var infowindow = new google.maps.InfoWindow();
        var marker, i;
        var image='../../../../../image/rider.png'
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][2], locations[i][3]),
                map: map,
                icon:{
                    url: image,
                    labelOrigin: new google.maps.Point(10, -10)
                },
                title:locations[i][0],
                label: {
                    text: locations[i][1],
                    color: '#fff',
                    fontSize: '14px',
                    fontWeight: 'bold',
                    className: "map-label"
                }
            });
            marker.addListener('click', function() {
                window.location.href = this.title;
            });
        }
    }
    window.initMap = initMap;
</script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap" ></script>
</body>
</html>
