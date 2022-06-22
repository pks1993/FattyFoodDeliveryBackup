{{-- @extends('admin.layouts.master')

@section('css')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <div class="flash-message" id="successMessage">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('fatty/main/admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Branch</li>
                        <li class="breadcrumb-item active">Lists</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{route('fatty.admin.branch.create')}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add Branch</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="zones" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th class="text-left">BranchName</th>
                                <th class="text-left">ZoneName</th>
                                <th class="text-left">CityName</th>
                                <th class="text-left">StateName</th>
                                <th class="text-left">CreateAdmin</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($branch as $bra)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="text-left">{{$bra->branch_name}}</td>
                                    <td class="text-left">{{$bra->zone->zone_name}}</td>
                                    <td class="text-left">{{$bra->city->city_name_mm}}</td>
                                    <td class="text-left">{{$bra->state->state_name_mm}}</td>
                                    <td class="text-left">{{$bra->user->name}}</td>
                                    <td class="btn-group">
                                        <a href="{{route('fatty.admin.branch.edit',['branch_id'=>$bra->branch_id])}}" class="btn btn-primary btn-sm mr-1"><i class="fa fa-edit"></i></a>

                                        <form action="{{route('fatty.admin.branch.destroy', $bra->branch_id)}}" method="post" onclick="return confirm('Do you want to delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script>
    $(function () {
        $("#zones").DataTable();
    });
</script>
<script type="text/javascript">
    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);
</script>
@endpush --}}

{{-- <iframe
  width="300"
  height="170"
  frameborder="0"
  scrolling="no"
  marginheight="0"
  marginwidth="0"
  src="https://maps.google.com/maps?q='+YOUR_LAT+','+YOUR_LON+'&hl=es&z=14&amp;output=embed"
 > --}}

 {{-- <iframe width="100%"
 height="100%"
 frameborder="0"
 scrolling="no"
 marginheight="0"
 marginwidth="0"
src = "https://maps.google.com/maps?q=21.9293083,96.1116005&hl=es;z=14&amp;output=embed"></iframe> --}}
{{-- <iframe width="100%" height="100%" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" style="border:0" src="https://maps.google.com/maps?saddr=21.9339885,96.110661&daddr=21.938852968794,96.136361720505%20to:21.929273415891313,96.1115577444434&hl=es;z=19&amp;output=embed" allowfullscreen></iframe> --}}
{{-- <iframe width="100%"
        height="100%"
        frameborder="0"
        scrolling="no"
        marginheight="0"
        marginwidth="0"
        src="https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=333+E+34th+St,+New+York,+NY&aq=1&oq=333&sll=37.269174,-119.306607&sspn=16.742323,33.815918&ie=UTF8&hq=&hnear=333+E+34th+St,+New+York,+10016&t=m&z=14&ll=40.744403,-73.974467&output=embed">
</iframe> --}}

{{-- <script
src="http://maps.googleapis.com/maps/api/js">
</script>
<script>
function initialize() {
    var latitude = 21.9293083;
    var longitude = 96.1116005;
    var zoom = 14;

    var LatLng = new google.maps.LatLng(latitude, longitude);

  var mapProp = {
    center: LatLng,
    zoom:14,
    mapTypeId:google.maps.MapTypeId.ROADMAP
  };
  var map=new google.maps.Map(document.getElementById("googleMap"), mapProp);
  var marker = new google.maps.Marker({
      position: LatLng,
      map: map,
      title: 'Customer',
      draggable: true
    });
  google.maps.event.addListener(marker, 'Customer', function(event) {

      document.getElementById('la').value = event.latLng.lat();
      document.getElementById('lo').value = event.latLng.lng();



});
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

        <div id="googleMap" style="width:auto;height:400px;"></div>

        <input type="hidden" id="la" name="la">
        <input type="hidden" id="lo" name="lo"> --}}


        {{-- <!DOCTYPE html>
        <html>
          <head>
            <title>Marker Labels</title>
            <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
            <link rel="stylesheet" type="text/css" href="./style.css" />
            <script src="./index.js"></script>
            <style>
                /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
#map {
  height: 100%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
            </style>
          </head>
          <body>
            <div id="map"></div>

            <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
            <script
              src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKbibh4Mr7_vpm2f-n0lo48TPbQKv2JHI&callback=initMap&v=weekly"
              async
            ></script>
            <script>
                // In the following example, markers appear when the user clicks on the map.
// Each marker is labeled with a single alphabetical character.
const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
let labelIndex = 0;

function initMap() {
  const bangalore = { lat: 12.97, lng: 77.59 };
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 12,
    center: bangalore,
  });

  // This event listener calls addMarker() when the map is clicked.
  google.maps.event.addListener(map, "click", (event) => {
    addMarker(event.latLng, map);
  });
  // Add a marker at the center of the map.
  addMarker(bangalore, map);
}

// Adds a marker to the map.
function addMarker(location, map) {
  // Add the marker at the clicked location, and add the next-available label
  // from the array of alphabetical characters.
  new google.maps.Marker({
    position: location,
    label: labels[labelIndex++ % labels.length],
    map: map,
  });
}
            </script>
          </body>
        </html> --}}

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
                  height: 100%;
                }
                html,
                body  {
                height: 100%;
                margin: 0;
                padding: 30px 10px 30px 10px;
                }
                .map-label{
                    background-color: #000000;
                }
            </style>
        </head>

        <body>
            {{-- <textarea id="location">{{ $locations }}</textarea> --}}
            <div id="map"></div>

            <script type="text/javascript">
                function initMap() {
                    const myLatLng = { lat: 22.9665, lng: 97.7525 };
                    const map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 14,
                        center: myLatLng,
                    });


                    var locations =[
                                    ['riders/view/15', 22.974384,97.761361],
                                    ['Pune', 22.936126,97.751064],
                                    ['Bhopal ', 22.964286,97.754655],
                                    ['Agra', 22.942919,97.75457],
                                    ['Delhi', 22.9444281,97.741002],
                                    ['Rajkot', 22.930166,97.751226],
                                ];
                    // var locations=document.getElementById('location').value;
                    console.log(locations);

                    const contentString =
                    '<div id="content">' +
                    '<div id="siteNotice">' +
                    "</div>" +
                    '<h1 id="firstHeading" class="firstHeading">Uluru</h1>' +
                    '<div id="bodyContent">' +
                    "<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large " +
                    "sandstone rock formation in the southern part of the " +
                    "Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) " +
                    "south west of the nearest large town, Alice Springs; 450&#160;km " +
                    "(280&#160;mi) by road. Kata Tjuta and Uluru are the two major " +
                    "features of the Uluru - Kata Tjuta National Park. Uluru is " +
                    "sacred to the Pitjantjatjara and Yankunytjatjara, the " +
                    "Aboriginal people of the area. It has many springs, waterholes, " +
                    "rock caves and ancient paintings. Uluru is listed as a World " +
                    "Heritage Site.</p>" +
                    '<p>Attribution: Uluru, <a href="https://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">' +
                    "https://en.wikipedia.org/w/index.php?title=Uluru</a> " +
                    "(last visited June 22, 2009).</p>" +
                    "</div>" +
                    "</div>";



                    // var infowindow = new google.maps.InfoWindow();
                    const infowindow = new google.maps.InfoWindow({
                        content: contentString,
                    });

                    var marker, i;
                    var image='../../../../../image/rider.png'

                    for (i = 0; i < locations.length; i++) {
                            marker = new google.maps.Marker({
                            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                            map: map,
                            icon:{
                                url: image,
                                labelOrigin: new google.maps.Point(10, -10)
                            },
                            title:locations[i][0],
                            // label: {text: locations[i][0], color: "white"}
                            label: {
                                    text: locations[i][0],
                                    color: '#FFF',
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    className: "map-label"
                                }

                          });

                          google.maps.event.addListener(marker, 'click', (function(marker, i) {
                            return function() {
                              infowindow.setContent(locations[i][0]);
                              infowindow.open(map, marker);
                                // var pos = map.getZoom();
                                // map.setZoom(16);
                                // map.setCenter(marker.getPosition());
                                // map.setCenter(marker.getPosition());
                                // window.setTimeout(function() {map.setZoom(pos);},3000);
                            }
                          })(marker, i));

                          marker.addListener('mouseover', function() {
                            infoWindow.setContent(locations[i][0]);
                            infowindow.open(map, marker);
                        });


                        // assuming you also want to hide the infowindow when user mouses-out
                        marker.addListener('mouseout', function() {
                            infowindow.close();
                        });

                        marker.addListener('click', function() {
                              window.location.href = this.title;
                        });

                    }
                }

                window.initMap = initMap;
            </script>

            <script type="text/javascript"
                src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap" ></script>

        </body>
        </html>
