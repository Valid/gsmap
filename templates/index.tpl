<!DOCTYPE html>
<html>
<head>
	<title>US Map Demo</title>
	
	<style>
	  #alert {
	    font-family: Arial, Helvetica, sans-serif;
	    font-size: 16px;
	    background-color: #ddd;
	    color: #333;
	    padding: 5px;
	    font-weight: bold;
	  }
	</style>
	
	<script src="js/lib/raphael.js"></script>
	<!-- <script src="scale.raphael.js"></script> -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>
	<script src="js/color.jquery.js"></script>
	<script src="js/jquery.usmap.js"></script>
	
	<script>
	$(document).ready(function() {
	  $('#map').usmap({
	    'stateSpecificStyles': {
	      'AK' : {fill: '#f00'}
	    },
	    'stateSpecificHoverStyles': {
	      'HI' : {fill: '#ff0'}
	    },
	    
	    'mouseoverState': {
	      'HI' : function(event, data) {
	        //return false;
	      }
	    },
	    'click' : function(event, data) {
	      $('#alert')
	        .text('Click '+data.name+' on map 1')
	        .stop()
	        .css('backgroundColor', '#ff0')
	        .animate({backgroundColor: '#ddd'}, 1000);
	    }
	  });
	  
	  $('#map2').usmap({
	    'stateStyles': {
	      fill: '#025', 
	      "stroke-width": 1,
	      'stroke' : '#036'
	    },
	    'stateHoverStyles': {
	      fill: 'teal'
	    },
	    
	    'click' : function(event, data) {
	      $('#alert')
	        .text('Click '+data.name+' on map 2')
	        .stop()
	        .css('backgroundColor', '#af0')
	        .animate({backgroundColor: '#ddd'}, 1000);
	    }
	  });
	  
	  $('#over-md').click(function(event){
	    $('#map').usmap('trigger', 'MD', 'mouseover', event);
	  });
	  
	  $('#out-md').click(function(event){
	    $('#map').usmap('trigger', 'MD', 'mouseout', event);
	  });
	});
	</script>
</head>
<body>
  <div id="alert">Click alerts</div>
  
  <div id="map" style="width: 930px; height: 630px; border: solid 3px red;"></div>
  
  <button id="over-md">mouseover MD</button> <button id="out-md">mouseout MD</button>
  <div id="map2" style="width: 300px; height: 300px;"></div>
</body>
</html>
