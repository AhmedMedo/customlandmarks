<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/jquery-1.11.3.min.js"></script>

  </head>
  <body>
    <div id="map"></div>
     <div id="floating-panel">
      <input  type=button id="hide" value="Hide Markers">
      <input type=button  id="show" value="Show All Markers">
      <input type="button" id="location" Value="My Location">
      <input type="button" id="rmdir" value="Clear routes">
    <input type="button" id="newmark" value="Add new mark">
      </br>
      <label>Search Landmarks</label>

      <select name="type" id="type">
          <option value=""></option>
          <option value="all">all</option>
          <option value="shop">shop</option>
          <option value="factory">factory</option>
          <option value="male">male</option>
          <option value="land">land</option>
          <option value="apartment">apartment</option>
          <option value="house">House</option>

      </select>
    </div>
    <div id="right-panel"></div>




   
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?signed_in=true&key=AIzaSyDLygkUyr4bvl3bJ0wyJ-STmUohtXZf5Fo&callback=initMap"
    async defer></script>

    <!-- Js Section -->
     <script type="text/javascript">

 $('#newmark').hide();
 
 


 var map;
 var allmarkers=[];//to get all markers
//initMap();
function initMap() {
  var pos = {};
  var center = new google.maps.LatLng(26.8206, 30.8025);
      var directionsService = new google.maps.DirectionsService;
      var directionsDisplay = new google.maps.DirectionsRenderer({map: map});

     var ShowButton = document.getElementById('show');
     var HideButton =document.getElementById('hide');
     var LocationButton=document.getElementById('location');
     var rmdir=document.getElementById('rmdir');
      var newmark=document.getElementById('newmark');
      var map = new google.maps.Map(document.getElementById('map'), {
        center: center,
        zoom: 6
      });


// var marker = new google.maps.Marker({
//             position: pointB, //map Coordinates where user right clicked
//             map: map,
//             draggable:true, //set marker draggable 
//             animation: google.maps.Animation.DROP, //bounce animation
//             icon: 'icons/pin_green.png' //custom pin icon
//         });


//         google.maps.event.addListener(marker,'dragend',function(){

//             alert(this.getPosition().lat());  
//         });
    // Search Section

      $('#type').change(function(){
        if(this.value == "all"){

          $.get("process.php", function (data) {
                  $(data).find("marker").each(function () {
                      var name    = $(this).attr('name');
                      var address   = '<p>'+ $(this).attr('address') +'</p>';
                      var type    = $(this).attr('type');
                      var point   = new google.maps.LatLng(parseFloat($(this).attr('lat')),parseFloat($(this).attr('lng')));
                      create_marker(point, name, map,address, false, false, false, "icons/pin_blue.png",false);
                  });
                });
              

        }
        else
        {
          HideMarkers();
                 $.ajax({
                  type: "POST",
                  url: "process.php",
                  data: {mytype : this.value},
                  success:function(data){
                      $(data).find("marker").each(function () {
                        var name    = $(this).attr('name');
                        var address   = '<p>'+ $(this).attr('address') +'</p>';
                        var type    = $(this).attr('type');
                        var point   = new google.maps.LatLng(parseFloat($(this).attr('lat')),parseFloat($(this).attr('lng')));
                        create_marker(point, name, map,address, false, false, false, "icons/pin_blue.png",false);
                        //alert(address);
                    });


                    
                  },
                  error:function (xhr, ajaxOptions, thrownError){
                    alert(thrownError); //throw any errors
                  }
                });
          

        }
      });
      //End Search seaction
      
      

      // make map responsive
        google.maps.event.addDomListener(window, "resize", function() {
         var center = map.getCenter();
         google.maps.event.trigger(map, "resize");
         map.setCenter(center); 
      });

      // Try HTML5 geolocation.
      google.maps.event.addDomListener(LocationButton,'click',function(){
              //   if (navigator.geolocation) {
              //   navigator.geolocation.getCurrentPosition(function(position) {
              //      pos = {
              //       lat: position.coords.latitude,
              //       lng: position.coords.longitude
              //     };

              //       // Create marker of User Location 
              //       create_marker(pos,'Your Location',map,' ',true,true,false,'icons/main.png',true);

              //      map.setCenter(pos);
              //   }, function(error) {



              //      switch(error.code) {
              //             case error.PERMISSION_DENIED:
              //                 alert("User denied the request for Geolocation.");
              //                 break;
              //             case error.POSITION_UNAVAILABLE:
              //                 alert("Location information is unavailable.");
              //                 break;
              //             case error.TIMEOUT:
              //                 alert("The request to get user location timed out.");
              //                 break;
              //             case error.UNKNOWN_ERROR:
              //                 alert("An unknown error occurred.");
              //                 break;
              //         }
              //         // if I can't get my location create a dragabble marker with a draggaple event and Egypt center
              //         create_marker(center,'put your location manually',map,'',true,true,false,'icons/main.png',true);

                                    
              //   },{enableHighAccuracy:true, timeout:60000, maximumAge:600000});
              // } else {
              //   // Browser doesn't support Geolocation
              //   alert("Browser doesn't support Geolocation and put yout location manually");

              // }
                      create_marker(center,'put your location manually',map,'',true,true,false,'icons/main.png',true);

              $('#location').hide();

      });


      // Show all markers on click
      google.maps.event.addDomListener(ShowButton, 'click', function() {
          ShowMarkers(map);
        });

      //Hide all markers on click
      google.maps.event.addDomListener(HideButton, 'click', function() {
          HideMarkers();
        });

          var icon = 'icons/pin_green.png';
        var EditForm = '<p><div class="marker-edit">'+
                    '<form action="ajax-save.php" method="POST" name="SaveMarker" id="SaveMarker">'+
                    '<label for="pName"><span>Place Name :</span><input type="text" name="pName" class="save-name" placeholder="Enter Title" maxlength="40" /></label>'+
                    '<label for="pDesc"><span>Description :</span><textarea name="pDesc" class="save-desc" placeholder="Enter Address" maxlength="150"></textarea></label>'+
                    '<label for="pType"><span>Type :</span> <select name="pType" class="save-type"><option value="shop">shop</option><option value="factory">factory</option>'+
                    '<option value="male">male</option>'+
                    '<option value="land">land</option>'+
                    '<option value="apartment">apartment</option>'+
                    '<option value="house">House</option></select></label>'+
                    '</form>'+
                    '</div></p><button name="save-marker" class="save-marker">Save Marker Details</button>';
          
            if(isMobile())
            
            {
              $('#newmark').show();
              google.maps.event.addDomListener(newmark,'click', function() {

                  

               
              create_marker(center,'New Map',map,EditForm,true,true,true,icon,false);

                });
              
            }
     



      // Right click 
     
         google.maps.event.addListener(map, 'rightclick', function(event) {

                  


            create_marker(event.latLng,'New Map',map,EditForm,true,true,true,icon,false);

      });

          
            
}


/*###################### Functions used##########################*/



    function isMobile(){
      return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    }
    /*
      Show all markres
    */
    function ShowMarkers(map)
    {
      for (var i = 0; i < allmarkers.length; i++) {
          allmarkers[i].setMap(map);
        }
    }


    /*
      Hide all markres
    */
  function HideMarkers()
  {
    for (var i = 0; i < allmarkers.length; i++) {
          allmarkers[i].setMap(null);
        }
  }
// Handling Error funtction if user location not found

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
      infoWindow.setPosition(pos);
      infoWindow.setContent(browserHasGeolocation ?
                            'Error: The Geolocation service failed.' :
                            'Error: Your browser doesn\'t support geolocation.');
    }

      /*
        Create markers
      */
   function  create_marker(Pos,MapTitle,Mapp,MapDesc,InfoOpenDefault,Draggable,Removable,IconPath,ToHide)
    {


      var marker = new google.maps.Marker({
            position: Pos, //map Coordinates where user right clicked
            map: Mapp,
            draggable:Draggable, //set marker draggable 
            animation: google.maps.Animation.DROP, //bounce animation
            icon: IconPath //custom pin icon
        });


      //Content structure of info Window for the Markers
    var contentString = $('<div class="marker-info-win">'+
    '<div class="marker-inner-win"><span class="info-content">'+
    '<h1 class="marker-heading">'+MapTitle+'</h1>'+
    MapDesc+ 
    '</span><button name="remove-marker" class="remove-marker" title="Remove Marker">Remove Marker</button>'+
    '</div></div>');  

    
    //Create an infoWindow
    var infowindow = new google.maps.InfoWindow();
    //set the content of infoWindow
    infowindow.setContent(contentString[0]);

    //Find remove button in infoWindow
    var removeBtn   = contentString.find('button.remove-marker')[0];
    var saveBtn   = contentString.find('button.save-marker')[0];

    //add click listner to remove marker button
    google.maps.event.addDomListener(removeBtn, "click", function(event) {
      remove_marker(marker);
    });
    
    if(typeof saveBtn !== 'undefined') //continue only when save button is present
    {
      //add click listner to save marker button
      google.maps.event.addDomListener(saveBtn, "click", function(event) {
        var mReplace = contentString.find('span.info-content'); //html to be replaced after success
        var mName = contentString.find('input.save-name')[0].value; //name input field value
        var mDesc  = contentString.find('textarea.save-desc')[0].value; //description input field value
        var mType = contentString.find('select.save-type')[0].value; //type of marker
        
        if(mName =='' || mDesc =='')
        {
          alert("Please enter Name and Description!");
        }else{
          save_marker(marker, mName, mDesc, mType, mReplace); //call save marker function
        }
      });
    }
    
            //if the marker is a draggable of user to put his location track his location and calc distance
              if(ToHide){
                   google.maps.event.addListener(marker,'dragend',function(){
                         mylocation=this.getPosition();
                       });
        
        

            } 
            //add click listner to save marker button    
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map,marker); // click on marker opens info window 
                //also when click by default get a direction to his location
              });
      
            if(InfoOpenDefault) //whether info window should be open by default
            {
              infowindow.open(map,marker);
            }

       
            var directionsService = new google.maps.DirectionsService;
              var directionsDisplay = new google.maps.DirectionsRenderer({
              map: map,
              suppressMarkers: true
            });
        google.maps.event.addListener(marker, "dblclick", function (event) {

            

        if(navigator.geolocation){
             navigator.geolocation.getCurrentPosition(locationHandler,showError);

                 function locationHandler(position)
                 {
                 var lat = position.coords.latitude;
                 var lng = position.coords.longitude;
                 var pos={lat,lng};
                directionsDisplay.setMap(Mapp);

                    calculateAndDisplayRoute(directionsService, directionsDisplay,pos,marker.getPosition());

                 }
                 
                 function showError(error){
                    switch(error.code) {
                      case error.PERMISSION_DENIED:
                        alert("User denied the request for Geolocation.");
                        break;
                      case error.POSITION_UNAVAILABLE:
                        alert("Location information is unavailable.");
                        break;
                      case error.TIMEOUT:
                        alert("The request to get user location timed out.");
                        break;
                      case error.UNKNOWN_ERROR:
                        alert("An unknown error occurred.");
                        break;
                    }
                    
                    
                   
                   
                 }
                 
    
        }else{
           //directionsDisplay.setMap(Mapp);

                    alert("Browser doesn't Geolocation location service");

                      calculateAndDisplayRoute(directionsService, directionsDisplay,mylocation,this.getPosition());
          
          
        }
              infowindow.close();

                   directionsDisplay.setMap(Mapp);
                   calculateAndDisplayRoute(directionsService, directionsDisplay,mylocation,this.getPosition());


            });

                google.maps.event.addDomListener(rmdir,'click',function(){

                        directionsDisplay.setMap(null);
                      });

         // add all markers to an array to control visibilty of the markers   
        if(!ToHide)
        {
          allmarkers.push(marker);
        }

    

   
  }

    //############### Remove Marker Function ##############
  function remove_marker(Marker)
  {
    
    /* determine whether marker is draggable 
    new markers are draggable and saved markers are fixed */
    if(Marker.getDraggable()) 
    {
      Marker.setMap(null); //just remove new marker
    }
    else
    {
      //Remove saved marker from DB and map using jQuery Ajax
      var mLatLang = Marker.getPosition().toUrlValue(); //get marker position
      var myData = {del : 'true', latlang : mLatLang}; //post variables
      $.ajax({
        type: "POST",
        url: "process.php",
        data: myData,
        success:function(data){
          Marker.setMap(null); 
          alert(data);
        },
        error:function (xhr, ajaxOptions, thrownError){
          alert(thrownError); //throw any errors
        }
      });
    }

  }
  
  //############### Save Marker Function ##############
  function save_marker(Marker, mName, mAddress, mType, replaceWin)
  {
    //Save new marker using jQuery Ajax
    var mLatLang = Marker.getPosition().toUrlValue(); //get marker position
    var myData = {name : mName, address : mAddress, latlang : mLatLang, type : mType }; //post variables
    console.log(replaceWin);    
    $.ajax({
      type: "POST",
      url: "process.php",
      data: myData,
      success:function(data){
        replaceWin.html(data); //replace info window with new html
        Marker.setDraggable(false); //set marker to fixed
        Marker.setIcon('icons/pin_green.png'); //replace icon
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(thrownError); //throw any errors
            }
    });
  }


  function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
      
      directionsService.route({
        origin: pointB,
        destination: pointA,
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC
      }, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
        } else {
          window.alert('Directions request failed due to ' + status);
        }
      });
    }
    function geocodeLatLng(point, map) {
        var geocoder = new google.maps.Geocoder;
        
        
        geocoder.geocode({'location': point}, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            if (results[1]) {
            
                results[1].formatted_address;
               
              
            } else {
              window.alert('No results found');
            }
          } else {
            window.alert('Geocoder failed due to: ' + status);
          }
        });

       

      }

    </script>

  </body>
</html>