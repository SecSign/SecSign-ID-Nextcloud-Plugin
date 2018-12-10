(function (OC) {
    'use strict';

    OC.Settings = OC.Settings || {};
    OC.Settings.SecSignID = OC.Settings.SecSignID || {};

    $(function () {
        var view = new OC.Settings.SecSignID.View({
            el: $('#twofactor-totp-settings')
        });
        view.render();
    });
})(OC);
