/**
 * This script polls the server every 500ms to check if the user
 * has been successfully authenticated.
 * 
 * @author Björn Plüster
 * @copyright 2019 SecSign Technologies Inc.
 */
(function (OC, window, $) {
	'use strict';

	$(document).ready(function () {
		let polling = new Promise((resolve, reject) => {
			var getState = function (attempts, resolve, reject) {
				attempts += 1;
				if (attempts > 20) {
					reject('Login not authenticated. Please try again');
					return;
				}
				$.ajax({
					url: OC.generateUrl('/apps/secsignid/state/'),
					type: 'GET',
					success: function (data) {
						if(data.accepted){
							resolve(data);
						}else{
							setTimeout(getState,500,attempts,resolve,reject);
						}
					},
					error: function (data) {
						reject(data);
						setTimeout(getState,500,attempts,resolve,reject);
					}
				});
			}
			getState(0, resolve, reject);
		});
		polling.then((message) => {
			if(message.accepted){
				$("button").click();
			}
		});

	});
})(OC, window, jQuery);