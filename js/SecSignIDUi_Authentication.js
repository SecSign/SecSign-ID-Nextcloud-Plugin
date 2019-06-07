(function (OC, window, $) {
    $.fn.secsignUi = function (options) {

        //constants
        const ERROR = 1;
        const INFO = 2;
        const DEBUG = 3;
        const LOGLVL = ['', 'ERROR', 'INFO', 'DEBUG'];
        const SESSION_STATE_PENDING = 1;
        const SESSION_STATE_EXPIRED = 2;
        const SESSION_STATE_AUTHENTICATED = 3;
        const SESSION_STATE_DENIED = 4;
        const SESSION_STATE_SUSPENDED = 5;
        const SESSION_STATE_CANCELED = 6;
        const SESSION_STATE_FETCHED = 7;
        const SESSION_STATE_INVALID = 8;
        const SERVICEADDRESS = window.location.href;

        //globals
        var globalAppstate = null;
        var initialAppstate = null;
        var previousAppstate = null;
        var overlayState = 0;
        var pollingAuthSessionState = null;

        //errorcodes
        var globalErrorcodes = {
            //Plugin Errors
            0001: "Plugin Error, invalid settings.",
            0002: "Empty Response",

            //Enrollment errors
            1002: "API Error, possibly timeout",
            2001: "No SecSignID found or specified",
            2002: "API Error",

            2006: "ID exists already",
            //Auth Api errors
            3001: "Your authentication session expired, the timeout has been exceeded.",
            3002: "Your authentication session suspended",
            3003: "The service has canceled the authentication session",
            3004: "Your authentication session was denied in the mobile app",
            3007: "This authentication session has become invalid.",
            3008: "There was an error with the 2FA."
        };

        // default settings will be overriden by plugin initialization
        var defaults = {
            //Authentication
            serviceName: "SecSign Demo",
            showAccesspass: "true",
            pluginName: "secUi",
            restApiAuthentication: "https://httpapi.secsign.com",
            //General
            optAuthToken: null, //optional Auth Token for backend
            userName: "",
            userEmail: "",
            userSecSignId: null,
            customColor: "#1d9ad6",
            logMode: 1, //1:error, 2:info, 3:debug
            pollingInterval: 3000, //1000 = 1sec
            supportText: "Support",
            supportLink: "mailto:support@secsign.com",
            onError: null
        };

        //private functions
        var sanitizeInput = function (input) {
            return input.replace(/[^0-9a-zA-Z#-@_.:?=&/\+%]/g, '');
        };

        var settings = $.extend({}, defaults, options);
        $.each(settings, function (index, value) {
            if (typeof value == 'string') {
                settings[index] = sanitizeInput(value);
            }
        });


        var preCheck = function () {
            /*if(!settings.serviceName){
                setError(0001);
                logger(ERROR, "plugin settings serviceName empty");
                return false;
            }
            if(!settings.restApiAuthentication){
                setError(0001);
                logger(ERROR, "plugin settings restApiAuthentication empty");
                return false;
            }*/
            return true;

        }

        //AUTHENTICATION FUNCTIONS START (uses SecSignIDApi.js)

        //checks if id does exist on the server
        var requestAuthSession = function () {
            logger(DEBUG, 'request if authsession to ' + settings.restApiEnrollment);
            $.get(OC.generateUrl("/apps/secsignid/exists/"))
                .success(function (data) {
                    if (data) {
                        if (data.error != undefined) {
                            //show error
                            setErrorMessage(data["errormsg"]);
                        } else {
                            //display accesspass
                            var authsessionicondata = data.session.authsessionicondata;
                            if (settings.showAccesspass == "true") {
                                $("#secUi-pageAccesspass__noaccesspassicon").hide();
                                $("#secUi-pageAccesspass__accesspassicon").show();
                                $("#secUi-pageAccesspass__accesspass").attr("src", "data:image/png;base64," + authsessionicondata);
                            } else {
                                $("#secUi-pageAccesspass__accesspass").hide();
                                $("#secUi-pageAccesspass__noaccesspassicon").show();
                                $("#secUi-pageAccesspass__accesspassicon").hide();
                                $("#secUi-pageAccesspass__noaccesspass").fadeIn();
                            }

                            //set fields
                            $("#secUi-pageAccesspass_session").val(JSON.stringify(data.session));
                            $("#secUi-main__authsessionid").val(data.session.authsessionid);
                            $("#secUi-main__requestid").val(data.session.requestid);
                            $("#secUi-main__secsignid").val(data.session.secsignid);

                            // start polling
                            checkAuthSessionStateFunc();

                            //show the open App button if mobile device
                            handleDeviceButton();

                            //hide loader
                            loader(false, function () {});
                        }
                    }
                }).error(function (data){
                    setErrorMessage(data.responseJSON.message);
                });
        };

        //check the auth session for polling
        var checkAuthSessionStateFunc = function () {
            var array = JSON.parse($("#secUi-pageAccesspass_session").val());
            $.post(OC.generateUrl("/apps/secsignid/state/"), {
                session: array
            }).success(
                function (data) {
                    if (data) {
                        var authSessionState = parseInt(data);
                        switch (authSessionState) {
                            //authn cases
                            case SESSION_STATE_AUTHENTICATED:
                                $("#secUi-main__loginform").submit();
                                break;
                            case SESSION_STATE_PENDING:
                            case SESSION_STATE_FETCHED:
                                pollingAuthSessionState = window.setTimeout(checkAuthSessionStateFunc, settings.pollingInterval);
                                break;

                                //error cases
                            case SESSION_STATE_EXPIRED:
                                setError(3001);
                                break;
                            case SESSION_STATE_DENIED:
                                setError(3004);
                                break;
                            case SESSION_STATE_SUSPENDED:
                                setError(3002);
                                break;
                            case SESSION_STATE_CANCELED:
                                setError(3003);
                                break;
                            case SESSION_STATE_INVALID:
                                setError(3007);
                                break;
                            default:
                                setError(3008);
                        }
                    }
                }
            )
        };


        //cancel the auth session
        var cancelAuthSession = function () {
            var array = JSON.parse($("#secUi-pageAccesspass_session").val());
            $.post(OC.generateUrl('/apps/secsignid/cancelSession/'), {
                session: array
            }),
                function () {
                    logger(DEBUG, "Canceled AuthSession");
                }
        };


        //AUTHENTICATION FUNCTIONS END

        //UI FUNCTIONS START

        //handles the button for mobile devices to open the SecSignID App
        var handleDeviceButton = function () {
            //handle button for login on mobile devices
            if (!jQuery.device) {
                jQuery.device = {};
            }
            jQuery.device.mobile = /(Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone)/i.test(navigator.userAgent);
            if (jQuery.device.mobile) {
                $("#open-secsignid-app").show();
            }
            $("#open-secsignid-app").click(function (event) {
                SecSignIDApi.openMobileApp({
                    "secsignid": "$secsignid",
                    "authsessionid": "$authsessionid",
                    "returnurl": String(window.location),
                    "idserverurl": "$idserverurl"
                });
            });
        }

        //handle loader
        var loader = function (show, callback) {
            if (show) {
                $('#secUi-pageLoad').fadeIn("fast", function () {
                    $('#secUi-pageLoad__loaderContainer').delay(500).fadeIn();
                    callback();
                });
            } else {
                $('#secUi-pageLoad').fadeOut("fast", function () {
                    $('#secUi-pageLoad__loaderContainer').fadeOut();
                    callback();
                });
            }

        }

        //initialize the plugin and set everything up
        var init = function () {

            //add secsign badge
            $('.secUi-main__wrapper').append('<div class="secUi-main__badge secUi-custbgcolor"></div>');

            //show support Links
            if (settings.supportText != "" && settings.supportLink != "") {
                $('<a target="_blank" href="' + settings.supportLink + '" class="secUi-main__supportlink secUi-custcolor secUi-custborder">' + settings.supportText + '</a>').
                insertBefore('.secUi-main__helplink');

            }

            //handle custom custom color
            var c = settings.customColor;
            $(
                '<style>' +
                '.secUi-custcolor{color: ' + c + ' !important;}' +
                '.secUi-custbutton{color: ' + c + ' !important; border-color: ' + c + ' !important;}' +
                '.secUi-custbutton:hover{color: #fff !important; background-color: ' + c + ' !important;}' +
                '.secUi-custbgcolor{background-color: ' + c + ' !important;}' +
                '.secUi-custappcolor:hover{color: #fff !important; background-color: ' + c + ' !important;}' +
                '.secUi-custborder{border-color: ' + c + ' !important;}' +
                '</style>'
            ).appendTo('head');
            logger(DEBUG, 'set up custom color');
        }

        //logger for controlling debug, info and error messages
        var logger = function (type, logmsg) {
            if (type <= settings.logMode) {
                console.log("secsign (" + LOGLVL[type] + ") - " + logmsg);
            }
        }

        //progressbar
        var setProgress = function (percentage) {
            $('.secUi-progress__bar').css('width', percentage + '%');
        }

        //check responses for errors
        var noErrors = function (response) {
            if (!response) {
                setError(0002);
                return false;
            }
            if (response.error) {
                setErrorMessage(response.error + ": " + response.errormsg);
                return false;
            }
            return true;
        }

        //error handling
        var setErrorMessage = function (errormessage) {
            logger(DEBUG, 'change appstate to pageError');
            globalAppstate = "pageError";
            endPolling();
            logger(ERROR, errormessage);

            $('.secUi-page').hide();
            $('#secUi-pageError__errorCode').text('');
            $('#secUi-pageError__errorMsg').text(errormessage);
            $('#secUi-pageError').show();
            loader(false, function () {});
        }
        var setError = function (errorcode) {
            logger(DEBUG, 'change appstate to pageError');
            globalAppstate = "pageError";
            endPolling();
            logger(ERROR, globalErrorcodes[errorcode] + " (" + errorcode + ")");

            $('.secUi-page').hide();
            $('#secUi-pageError__errorCode').text(errorcode);
            $('#secUi-pageError__errorMsg').text(globalErrorcodes[errorcode]);
            $('#secUi-pageError').show();
            loader(false, function () {});
        }

        //stops all polling
        var endPolling = function () {
            logger(DEBUG, 'end all polling');
            window.clearTimeout(pollingAuthSessionState);
        }

        //handle appstate
        var setAppState = function (appstate) {
            logger(DEBUG, 'change appstate to ' + appstate);
            globalAppstate = appstate;

            //appState specifics
            switch (appstate) {

                //general appstates

                case "pageInit":
                    setProgress(0);
                    endPolling();
                    setAppState(initialAppstate);
                    break;

                    //authentication related appstates

                case "pageUserCredentials":
                    setProgress(10);
                    $('.secUi-page').hide();
                    $('#secUi-pageUserCredentials').show();
                    loader(false, function () {});
                    break;

                case "pageAccesspass":
                    setProgress(80);
                    $('.secUi-page').hide();
                    $('.secUi-main__displayid').text(settings.userSecSignId);
                    $('#secUi-pageAccesspass').show();
                    //has loader in callback
                    loader(true, function () {});
                    requestAuthSession();
                    break;

                case "cancelAuth":
                    setProgress(10);
                    cancelAuthSession();
                    window.location.href =  $('a.two-factor-secondary:eq(1)').attr("href");
                    $('.secUi-page').hide();
                    //$('#secUi-' + initialAppstate).show();
                    loader(true, function () {});
                    break;

                case "pageAbout":
                    //setProgress(20);
                    $('.secUi-page').fadeOut();
                    $('#secUi-pageAbout').delay(500).fadeIn();
                    loader(false, function () {});
                    break;

                default:
                    // unknown appstate, doesn't have to be an error
                    logger(INFO, 'unknown appstate ' + appstate);
            }
        }

        //handle events

        //delete unsupported characters in ID field
        $('#secUi-main__newId').bind('keyup', '#secUi-main__secsignidInput', function () {
            var sanitized = $(this).val().replace(/[^0-9a-zA-Z-@_.]/g, '');
            $(this).val(sanitized);
        });

        //authentication related events

        //cancel authentication
        $('#secUi-main__container').on('click', '#secUi-pageAccesspass__cancelbtn', function () {
            loader(true, function () {
                setAppState('cancelAuth');
            });
        });

        //enrollment related events

        //enrollment cancel buttons point to initial appstate
        $('#secUi-main__container').on('click', '#secUi-pageQrRestore__cancelbtn, #secUi-pageQr__cancelbtn, #secUi-pageExistingID__cancelbtn,#secUi-pageClaim__cancelbtn,#secUi-pageError__cancelbtn', function () {
            loader(true, function () {
                setAppState("pageInit");
            });
        });

        //general button to cancel points to init appstate
        $('#secUi-main__container').on('click', '#secUi-pageCancel', function () {
            loader(true, function () {
                setAppState('init');
            });
        });

        //protected by secsignid badge
        $('#secUi-main__container').on('click', '.secUi-main__badge', function () {
            if (!overlayState) {
                previousAppstate = $('.secUi-page:visible:last');
                $('.secUi-page').fadeOut();
                $('#secUi-pageAbout').delay(500).fadeIn();
                overlayState = 1;
            }
        });

        $('#secUi-main__container').on('click', '#secUi-pageAbout__gobackbtn', function () {
            $('#secUi-pageAbout').fadeOut();
            previousAppstate.delay(500).fadeIn(function () {
                overlayState = 0;
            });
        });

        $('a.two-factor-secondary').on('click', function (){
            loader(true, function(){});
            cancelAuthSession();
        });


        //public functions
        return {

            //start enrollment and set up UI
            startApp: function (appstate, error) {

                //save initial appstate
                if (appstate == 'pageAccesspass') {
                    initialAppstate = appstate;
                } else {
                    initialAppstate = appstate;
                }

                //initialize UI
                init();

                //check if plugin settings are correct
                if (preCheck()) {
                    //show error page if parameter not empty
                    if (error) {
                        if (isNaN(error)) {
                            //show error page from message
                            setErrorMessage(error);
                        } else {
                            //show error page from code
                            setError(error);
                        }
                    } else {
                        //if no error go to specified appstate
                        setAppState(appstate);
                    }
                }
            },



        }
    }
}(OC, window, jQuery));