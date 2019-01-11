(function (OC, window, $) {
	'use strict';
	$(document).ready(function () {
		$("#enable_id").click(function (){
            console.log("clicked");
            let id = $("#secsignid_input").val();
            console.log(id);
            $.ajax({
                url: OC.generateUrl('/apps/secsignid/id/enable/'),
                type: 'POST',
                data: {secsignid: id},
                success: function(data){
                    $.ajax({
                        url: OC.generateUrl('apps/secsignid/ids'),
                        type: 'GET',
                        success: function (data){
                            console.log(data);
                        }
                    })
                }
            })
        })
	});
})(OC, window, jQuery);