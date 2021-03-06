jQuery(document).ready(function (OC, $) {

    var ssid;
    var secsignUi;

    $.get(OC.generateUrl("/apps/secsignid/id/")).success(function (data) {
        ssid = data;
        $.get(OC.generateUrl("/apps/secsignid/server/")).success(function (server) {
            $.get(OC.generateUrl("/apps/secsignid/onboarding/choice/")).success(function (choice_allowed){
                secsignUi = $('#secUi-container').secsignUi({
                    //Authentication
                    serviceName: "NextCloud",
                    showAccesspass: "true",
                    pluginName: "SecSign NextCloud Plugin",
                    restApiAuthentication: server.server+':'+server.serverport,
                    //Enrollment
                    restApiEnrollment: server.server+':'+server.serverport,
                    enrollmentMode: "qrsimple", //qrrestore,qrsimple,otp, none
                    enrollmentForceEmail: "true", //if userEmail empty it will show email input
                    enrollmentCustomIdAllowed: choice_allowed,
                    appUrlIos: "https://itunes.apple.com/us/app/secsign-id/id581467871?mt=8",
                    appUrlAndroid: "",
                    appUrlWinMobile: "",
                    appUrlWin: "",
                    appUrlOsx: "",
                    appUrlLinux: "",
                    //General
                    optAuthToken: null,
                    userName: "John.doe",
                    userEmail: "email@email.com",
                    userSecSignId: ssid,
                    customLogoUrl: "/img/logo.png",
                    customColor: "",
                    logMode: 1, //1:error, 2:info, 3:debug
                    pollingInterval: 500,
    
                    //callback funtions
                    onError: function () {
                        secsignUi.showError(this);
                    },
                });
    
                /* start UI in appstate, possible appstates:
                    - pageSecsignid (default, usually 2FA start point)
                    - pageAccesspass (usually 2FA start point with external MFA)
                    - pageCredentials (usually startpoint for 2SA)
                    - pageApps (usually enrollment start point)
                    - pageError (usually start point when backend error occured)
                    - prepareQr
                    - pageQr
                    - pageQrDesktop
                    - pageExistingID
                    - pageEnterEmail
                    - pageEmailConfirm
                    - pageClaim*/
    
                //secsignUi.startApp('pageApps');
                secsignUi.startApp("pageApps", false);
            });            
        });
    });
}(OC, jQuery));