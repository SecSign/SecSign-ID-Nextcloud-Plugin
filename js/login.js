let url = OC.generateUrl('/apps/secsignid/login/state');
let attempts = 0;
function checkAuthenticated(){
	$.get(url, function(response){
		checkAuthenticated();
		attempts+=1;
		console.log(attempts);
	})
}
checkAuthenticated();

