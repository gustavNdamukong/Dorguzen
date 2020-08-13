/*------------------------------
 * Copyright 2016 Pixelized
 * http://www.pixelized.cz
 *
 * umarket theme v1.1
------------------------------*/

/*------------------------------
	WINDOW SCROLL
------------------------------*/
$(window).scroll(function(){
	
	/*------------------------------
		FIXED NAVBAR
	------------------------------*/	
	if($(window).width() > 767) {
		if($(window).scrollTop() > 165) {
			$('header.navbar-default').addClass('navbar-small');
			if($('header.navbar-default').hasClass("navbar-static-top")) {
				$('header.navbar-default').removeClass('navbar-static-top');
				$('header.navbar-default').addClass('navbar-fixed-top');
				$('body').css("padding-top","100px");
			}
		}
		
		else if($(window).scrollTop() > 40) {
			$('header.navbar-default').removeClass('navbar-small');
			if($('header.navbar-default').hasClass("navbar-static-top")) {
				$('header.navbar-default').removeClass('navbar-static-top');
				$('header.navbar-default').addClass('navbar-fixed-top');
				$('body').css("padding-top","100px");
			}
		}
				
		else {
			$('header.navbar-default').removeClass('navbar-fixed-top');
			$('header.navbar-default').addClass('navbar-static-top');
			$('body').css("padding-top","0px");
		}
	}
	
	else {
		if($(window).scrollTop()) {
			if($('header.navbar-default').hasClass("navbar-static-top")) {
				$('header.navbar-default').addClass('navbar-offset');
				$('header.navbar-default').removeClass('navbar-static-top');
				$('header.navbar-default').addClass('navbar-fixed-top');
				$('body').css("padding-top","60px");
			}
		}
	}
	
	/*------------------------------
		TRANSPARENT NAVBAR
	------------------------------*/
	if($(window).width() > 1199) {
		if($(window).scrollTop() > 300) {
			$('header.navbar-transparent').addClass('navbar-offset');
		}
		else {
			$('header.navbar-transparent').removeClass('navbar-offset');
		}
	}
	else if($(window).width() > 991) {
		if($(window).scrollTop() > 200) {
			$('header.navbar-transparent').addClass('navbar-offset');
		}
		else {
			$('header.navbar-transparent').removeClass('navbar-offset');
		}
	}
	else if($(window).width() > 767) {
		if($(window).scrollTop() > 100) {
			$('header.navbar-transparent').addClass('navbar-offset');
		}
		else {
			$('header.navbar-transparent').removeClass('navbar-offset');
		}
	}
	else {
		if($(window).scrollTop()) {
			$('header.navbar-transparent').addClass('navbar-offset');
		}
	}
	
	/*------------------------------
		SCROLL TOP
	------------------------------*/
	if($(window).scrollTop() > 300) {
		$("#scrolltop").addClass("in");
	}
	else {
		$("#scrolltop").removeClass("in");
	}
});

/*------------------------------
	DOCUMENT READY
------------------------------*/
$(document).ready(function() {		
		
	/*------------------------------
		SCROLL FUNCTION
	------------------------------*/
	function scrollToObj(target, offset, time) {
		$('html, body').animate({scrollTop: $( target ).offset().top - offset}, time);
	}
	
	$("a.scroll[href^='#']").click(function(){
		scrollToObj($.attr(this, 'href'), 80, 1000);
		return false;
	});
	
	$("#scrolltop").click(function() {
		scrollToObj('body',0, 1000);
    });
	
	/*------------------------------
		COMPARE TABLE
	------------------------------*/
	$('#table-compare').dragtable({
		dragHandle:'.fa-arrows',
		dragaccept:'.accept'
	});
	
	/*------------------------------
		TOOLTIP INIT
	------------------------------*/
	$('.widget-color .checkbox label').tooltip();
	
	/*------------------------------
		SCROLLSPY INIT
	------------------------------*/
	$('body').scrollspy({ target: '#scrollspy-nav', offset:100 });
	
	/*------------------------------
		GRID/LIST TOGGLE
	------------------------------*/
	$('#toggle-grid').click(function(e) {
        $(this).addClass('active');
		$('#toggle-list').removeClass('active');
		$('#products').fadeOut(300, function() {
			$(this).addClass('grid').removeClass('list').fadeIn(300);
		});
    });
	
	$('#toggle-list').click(function(e) {
        $(this).addClass('active');
		$('#toggle-grid').removeClass('active');
		$('#products').fadeOut(300, function() {
			$(this).addClass('list').removeClass('grid').fadeIn(300);
		});
    });
	
	/*------------------------------
		NAVBAR SEARCH
	------------------------------*/
	$('.navbar-search').click(function(e) {
		if($(this).hasClass("open")) {
			$(this).find("i").removeClass("fa-times");
			$(this).find("i").addClass("fa-search");
		}
		else {
			$(this).find("i").removeClass("fa-search");
			$(this).find("i").addClass("fa-times");
		}
	});
	
	$('.navbar-search').on('hide.bs.dropdown', function () {
		$(this).find("i").removeClass("fa-times");
		$(this).find("i").addClass("fa-search");
	});
	
	/*------------------------------
		OWL CAROUSEL
	------------------------------*/

	$("#homepage-1-carousel").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		nav : true,
		navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	$("#homepage-2-carousel").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		nav : true,
		navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	$("#homepage-3-carousel").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		nav : true,
		navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	$("#homepage-4-carousel").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	$("#homepage-6-carousel").owlCarousel({
    	items : 1,
		loop : false,
		autoplay : false,
		dots : false,
  	});
	
	$("#testimonials-carousel").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	$("#blog-post-gallery").owlCarousel({
    	items : 1,
		loop : true,
		nav : true,
		dots : false,
		autoplay : true,
		navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
 	});
	
	$("#brands-carousel").owlCarousel({
		loop : true,
		autoplayHoverPause : true,
		autoplay : true,
		autoplayTimeout : 2000,
		smartSpeed : 1000,
		dots : false,
		responsive:{
			0:{
				items:2,
			},
			480:{
				items:3,
			},
			600:{
				items:4,
			},
			768:{
				items:5,
			},
			1200:{
				items:6,
			}
		}
  	});
	
	$("#product-carousel").owlCarousel({
    	items : 1,
		loop : true,
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	$('#product-quickview').on('shown.bs.modal', function (e) {
		
		$("#product-quickview").find(".product-carousel-wrapper").removeClass('hidden');
		
		$("#product-carousel-modal").owlCarousel({
			items : 1,
			animateOut: 'fadeOut',
			animateIn: 'fadeIn',
		});
	})
	
	$("#default-carousel").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		nav : true,
		navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
  	});
	
	$("#default-carousel-fade").owlCarousel({
    	items : 1,
		loop : true,
		autoplay : true,
		nav : true,
		navText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
		animateOut: 'fadeOut',
    	animateIn: 'fadeIn'
  	});
	
	/*------------------------------
		PRODUCT QUANTITY
	------------------------------*/			
	$('#qty-plus').click(function(e) {
		var temp = $('#qty').val();
		$('#qty').attr("value",parseInt(temp) + 1);
	});
	
	$('#qty-minus').click(function(e) {
		var temp = $('#qty').val();
		if(parseInt(temp) > 0) {
			$('#qty').attr("value",parseInt(temp) - 1);
		}
	});
	
	$('#modal-qty-plus').click(function(e) {
		var temp = $('#modal-qty').val();
		$('#modal-qty').attr("value",parseInt(temp) + 1);
	});
	
	$('#modal-qty-minus').click(function(e) {
		var temp = $('#modal-qty').val();
		if(parseInt(temp) > 0) {
			$('#modal-qty').attr("value",parseInt(temp) - 1);
		}
	});
	
	/*------------------------------
		WIDGET - PRICE FILTER
	------------------------------*/			
	var minimum = 20;
	var maximum = 300;
	
	$( "#slider-range" ).slider({
      range: true,
      min: minimum,
      max: maximum,
      values: [ minimum, maximum ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] );
		$( "#amount2" ).val( "$" + ui.values[ 1 ] );
      }
    });
	
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ));
	$( "#amount2" ).val( "$" + $( "#slider-range" ).slider( "values", 1 ));
	
	/*------------------------------
		YOUTUBE VIDEO BACKGROUND
	------------------------------*/
	$(".player").YTPlayer();
	
	/*------------------------------
		TWITTER QUERY
	------------------------------*/	
	var twitterOptions = {
		"id": '541286991938457600',
		"domId": '',
		"customCallback": handleTweets,
		"maxTweets": 2,
		"enableLinks": true,
		"showUser": false,
		"showImages": false,
		"showInteraction": false
	};
	
	function handleTweets(tweets) {
		var n = tweets.length;
		var i = 0;
		var element = document.getElementById('twitter-wrapper');
		var html = '<ul class="list-unstyled">';
		while(i < n) {
		  html += '<li>' + tweets[i] + '</li>';
		  i++;
		}
		html += '</ul>';
		element.innerHTML = html;
	}
	
	//twitterFetcher.fetch(twitterOptions);
	
	/*------------------------------
		GOOGLE MAP
	------------------------------*/	
	var regions = [
		{lat: 50.075854, lng: 14.421348},
		{lat: 40.715915, lng: -73.994263},
		{lat: 52.526258, lng: 13.430727},
		{lat: 48.857004, lng: 2.3645643}
	];

	var coordinates = [
		[{lat: 50.079378, lng: 14.426474}, "Krakovská 1307/22\n110 00 Prague 1, Czech Republic"], //PRAGUE
		[{lat: 50.090008, lng: 14.421004}, "Vězeňská 910/2\n110 00 Prague 1-Staré Město, Czech Republic"], //PRAGUE
		[{lat: 50.074917, lng: 14.383754}, "Na Hřebenkách 2\n150 00 Prague 5, Czech Republic"], //PRAGUE
		[{lat: 40.722355, lng: -73.998211}, "63 Crosby St\nNew York, NY 10012, USA"], //NEW YORK
		[{lat: 52.516808, lng: 13.393176}, "Bebelpl. 1\n10117 Berlin, Germany"], //BERLIN
		[{lat: 52.537379, lng: 13.425749}, "Jablonskistraße 16\n10405 Berlin, Germany"], //BERLIN
		[{lat: 52.516805, lng: 13.455275}, "Rigaer Str. 9\n10247 Berlin, Germany"], //BERLIN
		[{lat: 48.869031, lng: 2.333837}, "18 Rue Antin\n75002 Paris, France"] //PARIS
	];
	
	var markers = [];
	
	var map;
	
	var zoom = 13;
	if($(window).width() < 768) {
		zoom = zoom - 1;
	}
	
	// GOOGLE MAP INIT
	function initialize($) {
		var mapOptions = {
		  	zoom: zoom,
		  	center: regions[0],
		  	navigationControl: false,
		  	mapTypeControl: false,
		  	scaleControl: false,
		  	draggable: true,
			scrollwheel: false
		}
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		google.maps.event.addListenerOnce(map, 'idle', putmarkers);
	}
	
	function putmarkers($) {	
		for (var i = 0; i < coordinates.length; i++) {
			addMarkerWithTimeout(coordinates[i][0], coordinates[i][1], i+1, i * 400);
		}
	}
	
	if($("#map-canvas").length) {
		google.maps.event.addDomListener(window, 'load', initialize);
	}
	
	function addMarkerWithTimeout(position, text, store, timeout) {
	  	window.setTimeout(function() {
			
			var marker = new google.maps.Marker({
				position: position,
		  		map: map,
				title: text,
				url: "#marker-" + store,
		  		animation: google.maps.Animation.DROP
			});
					
			google.maps.event.addListener(marker, 'click', function() {
				scrollToObj(marker.url, 80, 800);
			});
			
			google.maps.event.addListener(marker, 'mouseover', function (event) {
				$("#map-tooltip").html("<p>" + marker.title + "</p>");
			});
			
			google.maps.event.addListener(marker, 'mouseout', function (event) {
				$("#map-tooltip").html('');
			});
						
			markers.push(marker);
	  	}, timeout);
	}
				
	$('#change-region').change(function(e) {
		var res = $(this).val();
		map.panTo(regions[res-1]);
		activaTab("region-" + res.toString());
    });
	
	function activaTab(tab){
		$('#tabs-regions .nav-tabs a[href="#' + tab + '"]').tab('show');
	};
	
	/*------------------------------
		MODAL ADVERTISING
	------------------------------*/
	$('#modalAdvertising').on('hide.bs.modal', function (e) {
		if(typeof(Storage) !== "undefined") {
			if($("#modal-hide").is(':checked')) {
				localStorage.setItem("modalhidden", "hidden");
			};
		}
	})
});

/*------------------------------
	WINDOW LOAD
------------------------------*/
$(window).load(function () {
	
	/*------------------------------
		MODAL ADVERTISING
	------------------------------*/
	if(typeof(Storage) !== "undefined") {
		if(localStorage.getItem("modalhidden") !== "hidden") {
			$('#modalAdvertising').modal('show');
		}
	}
	else {
		$('#modalAdvertising').modal('show');
	}
});

/*------------------------------
	CUSTOM FUNCTIONS
------------------------------*/
function print_window(){
	var e=window;
	e.document.close(),
	e.focus(),
	e.print(),
	e.close()
}