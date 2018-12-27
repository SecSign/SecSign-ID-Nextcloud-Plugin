(function($){

	$.when($.ready).then(function() {
		console.log('somethinghappened');
		$('two-factor-authentication').append(
				"<h2> SecSign 2FA </h2>"+
				"<h1> Enable two factor authentication with your SecSign ID </h1>"+
				"<form method='POST' >"+
  					"SecSign ID: <input type='text''' name='secsignid'>"+
  					"<input id='button' type='submit' value='Enable'>"+
				"</form>");
		
	});

})($);