(function (OC, window, $) {
    'use strict';

    function save(id) {
        $.post(OC.generateUrl('/apps/secsignid/id/enable/'), {
                secsignid: id
            },
            function (data) {
                console.log(data);
            }
        ).fail(function () {
            console.log("failed to save");
        });
    }

    let URL = OC.generateUrl('/apps/secsignid/ids/current/');
    console.log(URL);
    $.ajax({
        type: "GET",
        url: URL,
        success: function (data) {
            console.log(data);
            $(".lds-roller").hide();
            if (data.enabled == 1) {
                $("#enabled").show();
                $("#secsignid_input_en").val(data.secsignid)
                $("#change_id").click(function () {
                    save($("#secsignid_input_en").val());
                });
            } else {
                $("#disabled").show();
                $("#change_id").click(function () {
                    save($("#secsignid_input_dis").val());
                });
            }
        }
    }).fail(function () {
        console.log("failed");
    });


})(OC, window, jQuery);