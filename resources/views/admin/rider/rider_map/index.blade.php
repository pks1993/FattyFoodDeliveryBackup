<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rider Location Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style type="text/css">
        #map {
          height: 100%;
        }
        html,
        body  {
            height: 95%;
            margin: 0;
            padding: 0px 5px 0px 5px;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col">
            <a href="{{url('fatty/main/admin/riders')}}" class="btn btn-secondary btn-sm" style="margin-top: 10px;margin-bottom:5px;">
                <i class="fa fa-angle-double-left"></i> BackToAllRiders
            </a>
        </div>
        <div class="col">
            <a href="{{url('fatty/main/admin/foods/orders/lists')}}" class="btn btn-secondary btn-sm" style="margin-top: 10px;margin-bottom:5px;">
                <i class="fa fa-angle-double-left"></i> BackToAssignOrder
            </a>
        </div>
        <div class="col">
            <a href="{{url('fatty/main/admin/all_riders_location')}}" class="btn btn-info btn-sm" style="margin-top: 10px;margin-bottom:5px;">
                <i class="fa fa-location-arrow"></i> AllRiderLocation
            </a>
        </div>
        <div class="col">
            <a href="{{url('fatty/main/admin/all_riders_location/hasOrder')}}" class="btn btn-danger btn-sm" style="margin-top: 10px;margin-bottom:5px;">
                <i class="fa fa-location-arrow"></i> NotFreeRider
            </a>
        </div>
        <div class="col">
            <a href="{{url('fatty/main/admin/all_riders_location/hasNotOrder')}}" class="btn btn-success btn-sm" style="margin-top: 10px;margin-bottom:5px;">
                <i class="fa fa-location-arrow"></i> FreeRider
            </a>
        </div>
    </div>
    <div id="map"></div>

    <script type="text/javascript">
        function initMap() {
            // const myLatLng = { lat: 22.9665, lng: 97.7525 };
            var center_latitude=parseFloat("{{ $center_latitude }}");
            var center_longitude=parseFloat("{{ $center_longitude }}");
            const myLatLng ={ lat: center_latitude, lng: center_longitude };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 14,
                // zoom: 7,
                center: myLatLng,
            });
            var locations = [
                @foreach ($riders as $data)
                    [ "/fatty/main/admin/riders/detail/{{ $data->rider_id }}", "{{ $data->rider_user_name }}","{{ $data->rider_latitude }}", "{{ $data->rider_longitude }}","{{ $data->is_order }}" ],
                @endforeach
            ];

            var infowindow = new google.maps.InfoWindow();
            var marker, i;
            // var image='../../../../../image/rider.png'

            for (i = 0; i < locations.length; i++) {
                if(locations[i][4]==0){
                    var image="http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                }else{
                    var image="http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                }
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][2], locations[i][3]),
                    map: map,
                    icon:{
                        url: image,
                        labelOrigin: new google.maps.Point(10, -10)
                    },
                    title:locations[i][1],
                    rider_detail:locations[i][0],
                    label: {
                        text: locations[i][1],
                        color: '#fff',
                        fontSize: '14px',
                        fontWeight: 'bold',
                        className: "map-label"
                    }
                });
                marker.addListener('click', function() {
                    window.location.href = this.rider_detail;
                });
            }
        }
        window.initMap = initMap;
    </script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap" ></script>
</body>
</html>
