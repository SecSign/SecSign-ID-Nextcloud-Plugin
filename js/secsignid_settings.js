(function (OC, window, $) {
    'use strict';

    function save(id) {
        $.post(OC.generateUrl('/apps/secsignid/id/enable/'), {
                secsignid: id
            },
            function (data) {
                    $("#disabled").hide();
                    $("#enabled").show();
                    $("#disable").html("Disable");
                    $("#disable").click(function () {
                        disable();
                    });
                    $("#enabled input").val(data.secsignid);
                    $("#description").text("You have already added a SecSign ID protecting your account.")
                    $("#enabled div").html("<p class='animated fadeOut' style='color: green'>Successfully updated</p>");
                }
        ).fail(function () {
            //console.log("failed to save");
            alert("Failed to save SecSign ID, try again");
        });
    }

    function disable() {
        $.post(OC.generateUrl('/apps/secsignid/id/disable/'), null,
            function (data) {
                $("#enabled div").html("<p class='animated fadeOut' style='color: green'>2FA disabled</p>");
                $("#disable").html("Enable");
                $("#disable").click(function () {
                    save($("#secsignid_input_en"));
                })
                $("#description").text("You have a SecSign ID linked with your account, but 2FA is disabled. Press enable to activate 2FA.")

            }
        ).fail(function () {
            //console.log("failed to save");
            alert("Failed to save SecSign ID, try again");
        });
    }

    function setOnClick() {


    }

    let URL = OC.generateUrl('/apps/secsignid/ids/current/');
    $.ajax({
        type: "GET",
        url: URL,
        success: function (data) {
            $(".lds-roller").hide();
            if (data != null && data.secsignid != null) {
                $("#enabled").show();
                $("#secsignid_input_en").val(data.secsignid);
                if (data.enabled == 0) {
                    $("#description").text("You have a SecSign ID linked with your account, but 2FA is disabled. Press enable to activate 2FA.")
                    $("#disable").html("Enable");
                    $("#disable").click(function () {
                        save($("#secsignid_input_en").val());
                    });
                } else {
                    $("#disable").click(function () {
                        disable();
                    });
                }
                $("#change_id").click(function () {
                    save($("#secsignid_input_en").val());
                });

            } else {
                $("#disabled").show();
                $("#enable_id").click(function () {
                    save($("#secsignid_input_dis").val());
                });
            }
        }
    }).fail(function () {
        console.log("failed");
    });


})(OC, window, jQuery);