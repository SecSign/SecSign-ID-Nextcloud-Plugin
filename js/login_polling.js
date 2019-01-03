(function(OC, window, $, undefinded){
	'use strict';

	$(document).ready(function() {
		/*let polling = new Promise((resolve,reject)=>{
			var getState = function(attempts,resolve,reject){
				attempts+=1;
				if(attempts > 20)
				{
					reject('Login not authenticated. Please try again');
					return;
				}
				$.ajax({
					url: OC.generateUrl('/apps/secsignid/id/'),
					type: 'GET',
					dataType: 'json',
					success: function(data){
						resolve(data);
					},
					error: function(data){
						reject(data);
						//setTimeout(getState,500,attempts,resolve,reject);
					}
				});
			}
			getState(0,resolve,reject);
		});
		polling.then((message) => {
			console.log(message);
		});*/
		console.log(OC.generateUrl('/apps/secsignid/2fa_state/'));
		$.get(OC.generateUrl('/apps/secsignid/2fa_state/')).then(function(data){
			console.log(data);
		});
		
	});
})(OC,window,jQuery);