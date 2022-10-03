@extends('admin.layouts.master')

@section('css')

@endsection

@section('content')
<section class="content">
    <div class="col-md-12">
        <div class="text-center"><h5>Rider <b>"{{ $rider->rider_user_name }}'s"</b> Current Location</h5></div>
        <div class="row mt-2 mb-2" style="border-style:ridge">
            <iframe width="100%"
                height="450px"
                frameborder="0"
                scrolling="no"
                marginheight="0"
                marginwidth="0"
                src = "https://maps.google.com/maps?q={{ $rider->rider_latitude }},{{ $rider->rider_longitude }}&hl=es;z=14&amp;output=embed">
            </iframe>
        </div>
        <a href="{{url('fatty/main/admin/riders')}}" class="btn btn-primary btn-sm"><i class="fa fa-angle-double-left"></i> Back to <span>Rider lists</span></a>
    </div>
</section>
@endsection
@push('scripts')
@endpush


{{-- <html>
  <head>
    <title>Custom Markers</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <link rel="stylesheet" type="text/css" href="./style.css" />
    <script type="module" src="./index.js"></script>
  </head>
  <style>
      /*
 * Always set the map height explicitly to define the size of the div element
 * that contains the map.
 */
#map {
  height: 100%;
}

/*
 * Optional: Makes the sample page fill the window.
 */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
  </style>
  <body>
    <div id="map"></div>
    <input type="hidden" value="{{ $rider->rider_id }}" name="test">
    <!--
     The `defer` attribute causes the callback to execute after the full HTML
     document has been parsed. For non-blocking uses, avoiding race conditions,
     and consistent behavior across browsers, consider loading using Promises
     with https://www.npmjs.com/package/@googlemaps/js-api-loader.
    -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBpluBYT3DzB6erKq3sxpkiOAxZJvlynfk&callback=initMap&v=weekly" defer></script>
    <script>
        let map;
        let id=document.getElementById('test').value;

function initMap() {
  map = new google.maps.Map(document.getElementById("map"), {
    center: new google.maps.LatLng(-33.91722, 151.23064),
    zoom: 16,
  });

  const iconBase ="https://developers.google.com/maps/documentation/javascript/examples/full/images/";
  const icons = {
    parking: {
      icon: iconBase + "parking_lot_maps.png",
    },
    library: {
      icon: iconBase + "library_maps.png",
    },
    info: {
      icon: iconBase + "info-i_maps.png",
    },
  };
  const features = [
    {
      position: new google.maps.LatLng(-33.91721, 151.2263),
      type: "parking",
    },
  ];

  // Create markers.
  for (let i = 0; i < features.length; i++) {
    const marker = new google.maps.Marker({
      position: features[i].position,
      icon: icons[features[i].type].icon,
      map: map,
    });
  }
}

window.initMap = initMap;
    </script>
  </body>
</html> --}}

