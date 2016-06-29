
 var map;
//initMap();
function initMap() {
      var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -34.397, lng: 150.644},
        zoom: 6
      });
      var infoWindow = new google.maps.InfoWindow({
        map: map ,
        content : 'My location'

      });
      $.get("process.php", function (data) {
        $(data).find("marker").each(function () {
            var name    = $(this).attr('name');
            var address   = '<p>'+ $(this).attr('address') +'</p>';
            var type    = $(this).attr('type');
            var point   = new google.maps.LatLng(parseFloat($(this).attr('lat')),parseFloat($(this).attr('lng')));
            create_marker(point, name, map,address, false, false, false, "icons/pin_blue.png");
        });
      });

      // load markers 
        google.maps.event.addDomListener(window, "resize", function() {
         var center = map.getCenter();
         google.maps.event.trigger(map, "resize");
         map.setCenter(center); 
      });

      // Try HTML5 geolocation.
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
    var marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: 'mylocation'
      });
          marker.setMap(map);

          marker.addListener('click', function() {
        infoWindow.open(map, marker);
      });


          // infoWindow.setPosition(pos);
          // infoWindow.setContent('Location found. Zoom to see it');
           map.setCenter(pos);
        }, function() {
          handleLocationError(true, infoWindow, map.getCenter());
        });
      } else {
        // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
      }

        
      // Right click 
      var icon = 'icons/pin_green.png';
         google.maps.event.addListener(map, 'rightclick', function(event) {

                  var EditForm = '<p><div class="marker-edit">'+
                    '<form action="ajax-save.php" method="POST" name="SaveMarker" id="SaveMarker">'+
                    '<label for="pName"><span>Place Name :</span><input type="text" name="pName" class="save-name" placeholder="Enter Title" maxlength="40" /></label>'+
                    '<label for="pDesc"><span>Description :</span><textarea name="pDesc" class="save-desc" placeholder="Enter Address" maxlength="150"></textarea></label>'+
                    '<label for="pType"><span>Type :</span> <select name="pType" class="save-type"><option value="restaurant">Rastaurant</option><option value="bar">Bar</option>'+
                    '<option value="house">House</option></select></label>'+
                    '</form>'+
                    '</div></p><button name="save-marker" class="save-marker">Save Marker Details</button>';


            create_marker(event.latLng,'New Map',map,EditForm,true,true,true,icon);
 // var marker = new google.maps.Marker({
 //            position: event.latLng, //map Coordinates where user right clicked
 //            map: map,
 //            draggable:true, //set marker draggable 
 //            animation: google.maps.Animation.DROP, //bounce animation
 //            title:"Hello World!",
 //            icon: icon //custom pin icon
 //        });        
      });
}

// Handling Error funtction if user location not found

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
      infoWindow.setPosition(pos);
      infoWindow.setContent(browserHasGeolocation ?
                            'Error: The Geolocation service failed.' :
                            'Error: Your browser doesn\'t support geolocation.');
    }

   function  create_marker(Pos,MapTitle,Mapp,MapDesc,InfoOpenDefault,Draggable,Removable,IconPath)
    {


      var marker = new google.maps.Marker({
            position: Pos, //map Coordinates where user right clicked
            map: Mapp,
            draggable:Draggable, //set marker draggable 
            animation: google.maps.Animation.DROP, //bounce animation
            title:"Hello World!",
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
    
    //add click listner to save marker button    
    google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map,marker); // click on marker opens info window 
      });
      
    if(InfoOpenDefault) //whether info window should be open by default
    {
      infowindow.open(map,marker);
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
        Marker.setIcon('svg/h.png'); //replace icon
            },
            error:function (xhr, ajaxOptions, thrownError){
                alert(thrownError); //throw any errors
            }
    });
  }
