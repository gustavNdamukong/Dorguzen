var creditcards = { 
	list:[
		{
			brand: 			'American Express',
			image: 			'assets/images/creditcards/amex.png',
			verification: 	'^3[47][0-9]',
			separation: 	'^([0-9]{4})([0-9]{6})?(?:([0-9]{6})([0-9]{5}))?$',
			hidden: 		'**** ****** *[0-9][0-9][0-9][0-9]',
			accepted: 		true,
			length: 		15
		},
		{
			brand: 			'MasterCard',
			image: 			'assets/images/creditcards/mastercard.png',
			verification: 	'^5[1-5][0-9]',
			separation: 	'^([0-9]{4})([0-9]{4})?([0-9]{4})?([0-9]{4})?$',
			hidden: 		'**** **** **** [0-9][0-9][0-9][0-9]',
			accepted: 		true,
			length: 		16
		},
		{
			brand: 			'Visa',
			image: 			'assets/images/creditcards/visa.png',
			verification: 	'^4[0-9]',
			separation: 	'^([0-9]{4})([0-9]{4})?([0-9]{4})?([0-9]{4})?$',
			hidden: 		'**** **** **** [0-9][0-9][0-9][0-9]',
			accepted: 		true,
			length: 		16
		},
		{
			brand: 			'Discover',
			image: 			'assets/images/creditcards/discover.png',
			verification: 	'^6(?:011|5[0-9]{2})[0-9]',
			separation: 	'^([0-9]{4})([0-9]{4})?([0-9]{4})?([0-9]{4})?$',
			hidden: 		'**** **** **** [0-9][0-9][0-9][0-9]',
			accepted: 		false,
			length: 		16
		}
	], 
	active:null 
};

//On Keydown
$('input[name="creditcard"]').keydown(function(e){
  //Preset Data
  var card = $(this).val().replace(/[^0-9]/g,''),
	  trim = $.trim( $(this).val().slice(0,-1) );

  //Find the Credit Card
  for( var i=0; i<creditcards.list.length; i++ ){

	//Check the Type
	if(card.match( new RegExp(creditcards.list[i].verification) )){

	  //Set the Active Card
	  creditcards.active = i;

	  //Add Credit Card Icon
	  if( $('#credit-card-img').find('img').length == 0 ){

		//Remove any possible Error
		$('#credit-card-error').find('p').remove();

		//Add the Image
		$('#credit-card-img').html('<img src="'+creditcards.list[i].image+'" alt="'+creditcards.list[i].brand+'" />');
	  
	  }

	  //If the Credit Card is NOT accepted, Show the Error
	  if( !creditcards.list[i].accepted && $('#credit-card-error').find('p').length == 0 ){

		//Show Error
		$('#credit-card-error').html('<p>Creditcard Not Accepted</p>');
	  
	  }

	  //End the Loop
	  break;

	}

  }

  //Show Invalid Card
  if( creditcards.active == null && card.length > 4 && $('#credit-card-error').find('p').length == 0 ){

	//Show Error
	$('#credit-card-error').html('<p>Invalid Credit Card</p>');

  }

  //Preset they Key
  key = creditcards.active !== null? creditcards.active : 1 ;

  //If the Last Character is a String, Remove it
  if( e.keyCode == 8 && trim != $(this).val().slice(0,-1) ){
	
	//Set the New Value
	$(this).val( trim );

	//Stop from Completing
	e.preventDefault();

	//Return
	return;

  }

  //Limit the Length of the Card, Allow Keys
  if( card.length >= creditcards.list[ key ].length && $.inArray(e.keyCode, [37, 38, 39, 40, 46, 8, 9, 27, 13, 110, 190]) === -1 && !e.metaKey && !e.ctrlKey ){
	e.preventDefault();
	return;
  }

  //Add a Space if the Regex Passes
  if( new RegExp(creditcards.list[ key ].separation).exec( card ) && e.keyCode >= 48 && e.keyCode <= 57 ){
	$(this).val( $(this).val() + ' ' );
  }

  //Return
  return;

});

//On Key up Ensure Card is Validated
$('input[name="creditcard"]').keyup(function(e){

  //Get the Card
  var card = $(this).val().replace(/[^0-9]/,'');

  //Check if the Card is Active
  if( creditcards.active !== null && !card.match( new RegExp(creditcards.list[ creditcards.active ].verification) ) ){

	  //Remove any Existing Error
	  $('#credit-card-error').find('p').remove();

	  //If Not, Remove the Icon
	  $('#credit-card-img').find('img').remove();
	  
	  $('#credit-card-img').html('<i class="fa fa-credit-card"></i>');

	  //Set Active to NULL
	  creditcards.active = null;

  }else
  if( card.length < 4 ){

	//Remove Invalid Card Error
	$('#credit-card-error').find('p').remove();

  }

});

$('input[name="creditcard"]').on('paste',function(e){
  
  //Remove the Icon
  $('#credit-card-img').find('img').remove();
	  
  $('#credit-card-img').html('<i class="fa fa-credit-card"></i>');
  
  //Save the Element
  var el    = this;

  //Set Timeout to Run Function
  setTimeout(function(){

	//Save the Card
	var card = $(el).val().replace(/[^0-9]/g,'');

	//Remove all but numbers
	$(el).val( card );

	//Prepare the Keydown Event
	var e = jQuery.Event('keydown',{
	  which:    37,
	  keyCode:  37
	});

	//Trigger Keydown
	$(el).trigger(e).promise().done(function(e){

	  //Preset they Key
	  key = creditcards.active !== null? creditcards.active : 1 ;

	  //Force the Card Length
	  card.substr( 0 , creditcards.list[ key ].length );
	  
	  //Separate the Card
	  var separation  = new RegExp(creditcards.list[ key ].separation).exec( card ),
		  storage     = '';

	  //Find the Closest Separation Point
	  while( !separation && card.length > 1 ){
		storage     = card.charAt( card.length - 1 );
		card        = card.slice(0,-1);
		separation  = new RegExp(creditcards.list[ key ].separation).exec( card );
	  }

	  //If there was a Separation
	  if( separation ){

		//A Holder for all of the Separation that is defined
		var separated = [];

		//Remove all Undefined Separation Fields
		for( var i=0; i<separation.length; i++){
		  if( typeof separation[i] != 'undefined' ) separated.push( separation[i] );
		}

		//Build the String
		var string = separated.slice(1).join(' ') + (storage!=''? ' '+storage : '' )

		//Add the Separated Value
		$(el).val( string )

	  }        

	//End $(el).trigger(e).promise().dome(function(e){
	});

  //End setTimeout(function(){
  },0);

//End $(input[name="creditcard"]
});