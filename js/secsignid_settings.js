/**
 * This script is responsible for the SecSign ID settings for individual users.
 * It allows adding a SecSign ID, changing the ID and enabling or disabling
 * the 2FA.
 * 
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
(function (OC, window, $) {
    'use strict';

    $("#sec-goToSettings").on("click", function(){
        window.location.href = OC.generateUrl("/settings/user/security");
    });
})(OC, window, jQuery);