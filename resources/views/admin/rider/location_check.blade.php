<!DOCTYPE HTML>
<html>
   <head>
      <script type="text/javascript">
         function showLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            var latlongvalue = position.coords.latitude + ","+ position.coords.longitude;
            // var img_url = "https://maps.googleapis.com/maps/api/staticmap?center="+latlongvalue+"&amp;zoom=14&amp;size=1000x400&amp;key=AIzaSyAa8HeLH2lQMbPeOiMlM9D1VxZ7pbGQq8o";
            var img_url = "https://maps.google.com/maps?q="+latlongvalue+"&hl=es;z=14&amp;output=embed";
            // document.getElementById("mapholder").innerHTML ="<img src='"+img_url+"' style='width:50%;height:50%'>";
            document.getElementById("mapholder").innerHTML ="<iframe src='"+img_url+"' style='width:100%;height:100%'></iframe>";
            document.getElementById("demo").value =latlongvalue;
            }

            navigator.geolocation.getCurrentPosition(
                showLocation,errorHandler,function( position ){ // success cb
                    console.log( position.coords.latitude,position.coords.longitude );
                },
                function(){ // fail cb
                }
            );
         function errorHandler(err) {
            if(err.code == 1) {
               alert("Error: Access is denied!");
            } else if( err.code == 2) {
               alert("Error: Position is unavailable!");
            }
         }
        //  function getLocation(){
        //     if(navigator.geolocation){
        //        // timeout at 60000 milliseconds (60 seconds)
        //        var options = {timeout:60000};
        //        navigator.geolocation.getCurrentPosition(showLocation, errorHandler, options);
        //     } else{
        //        alert("Sorry, browser does not support geolocation!");
        //     }
        //  }
      </script>
      <!-- <script type="text/javascript">
        setTimeout(function(){
          location = ''
        },15000)
      </script> -->
   </head>
   <body>
       <!-- <div id="mapholder" style="width: 100%;height:500px"></div> -->
       <input type="text" style="font-size: 20px;margin-top:20px" id="demo">
      {{-- <form>
         <input type="button" onclick="getLocation();" value="Get Location"/>
      </form> --}}
   </body>
</html>
