(function (OC, $) {
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

        const QRSIMPLE = "qrsimple";
        const QRRESTORE = "qrrestore";
        const OTP = "otp";

        //globals
        var globalAppstate = null;
        var initialAppstate = null;
        var previousAppstate = null;
        var overlayState = 0;
        var pollingAuthSessionState = null;
        var pollingEnrollmentExist = null;
        var pollingEnrollmentRestored = null;

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
            //Enrollment
            restApiEnrollment: "/",
            enrollmentMode: QRSIMPLE, //qrrestore,qrsimple,otp,none
            enrollmentForceEmail: "false", //if userEmail empty it will show email input
            enrollmentCustomIdAllowed: true,
            appUrlIos: "https://itunes.apple.com/us/app/secsign-id/id581467871?mt=8",
            appUrlAndroid: "",
            appUrlWinMobile: "",
            appUrlWin: "",
            appUrlOsx: "",
            appUrlLinux: "",
            //General
            optAuthToken: null, //optional Auth Token for backend
            userName: "",
            userEmail: "",
            userSecSignId: null,
            customLogoUrl: "",
            customColor: "#1d9ad6",
            logMode: 1, //1:error, 2:info, 3:debug
            pollingInterval: 3000, //1000 = 1sec
            onError: null
        };

        //private functions
        var sanitizeInput = function (params) {
            //...
            return params;
        };
        var settings = $.extend({}, defaults, sanitizeInput(options));
        var secSignIdApi = new SecSignIDApi({
            posturl: settings.restApiAuthentication,
            pluginname: settings.pluginName
        });

        var preCheck = function () {
            if (!settings.serviceName) {
                setError(0001);
                logger(ERROR, "plugin settings serviceName empty");
                return false;
            }
            if (!settings.restApiAuthentication) {
                setError(0001);
                logger(ERROR, "plugin settings restApiAuthentication empty");
                return false;
            }
            var eM = settings.enrollmentMode;
            if (!eM || (eM != QRSIMPLE && eM != QRRESTORE && eM != OTP && eM != "none")) {
                setError(0001);
                logger(ERROR, "plugin settings enrollmentMode empty or not one of the following: qrsimple, qrrestore, otp, none");
                return false;
            }
            if (eM == "none") {
                //delete all enrollment screens

            }
            return true;

        }

        //AUTHENTICATION FUNCTIONS START (uses SecSignIDApi.js)

        //request Auth Session and get the accesspass
        var requestAuthSession = function () {
            console.log("ACCESSPASS: " + settings.showAccesspass);
            secSignIdApi.requestAuthSession({
                secsignid: settings.userSecSignId,
                servicename: settings.serviceName,
                serviceaddress: SERVICEADDRESS,
                callbackFunction: function (responseMap) {
                    console.log(responseMap);
                    if (responseMap) {
                        if (responseMap["error"] != undefined) {
                            //show error
                            setErrorMessage(responseMap["errormsg"]);
                        } else {
                            //display accesspass
                            var authsessionicondata = responseMap["authsessionicondata"];
                            console.log("AP: " + settings.showAccesspass);
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
                            $("#secUi-main__authsessionid").val(responseMap["authsessionid"]);
                            $("#secUi-main__requestid").val(responseMap["requestid"]);
                            $("#secUi-main__secsignid").val(responseMap["secsignid"]);

                            // start polling
                            checkAuthSessionStateFunc();

                            //show the open App button if mobile device
                            handleDeviceButton();

                            //hide loader
                            loader(false, function () {});
                        }
                    }
                }
            });
        }


        //check the auth session for polling
        var checkAuthSessionStateFunc = function () {
            var array = JSON.parse($("#secUi-pageAccesspass_session").val());
            console.log(array);
            $.post(OC.generateUrl("/apps/secsignid/state/"), {session: array}).success(
                function (data){
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
            $.post(OC.generateUrl('/apps/secsignid/cancel/')), function (){
				//console.log("cancelled auth session");
			}
        };


        //AUTHENTICATION FUNCTIONS END

        //----------------------------

        //ENROLLMENT FUNCTIONS START

        //polling for restore status during enrollment
        var testRestoreOnServer = function (id) {
            $.post("/idp/Authn/Secsign/", {
                    "appstate": "enrollment_getqrrestore",
                    "secsignid": id
                })
                .done(function (data) {
                    if (data == 'true') {
                        $("#secsignid-qrscan-form").submit();
                    } else {
                        window.setInterval(testRestoreOnServer(id), 5000);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown, data) {
                    console.log("fail");
                    window.setInterval(testRestoreOnServer(id), 5000);
                });
        };

        //gets a qr code from the id server via bridge (create or restore)
        var requestCode = function (options, callback) {

            if (options.secsignid) {
                if (options.type == QRSIMPLE) {
                    logger(DEBUG, 'get simple QR code for ' + options.secsignid);
                    options.appstate = "enrollment_getqrsimple";

                } else if (options.type == QRRESTORE) {
                    logger(DEBUG, 'get restore QR code for ' + options.secsignid);
                    options.appstate = "enrollment_getqrrestore";
                }

                //make request
                $.post(settings.restApiEnrollment, options, null, "json")
                    .done(function (data) {
                        callback(data);
                        return;
                    })
                    .fail(function (jqXHR, textStatus, errorThrown, data) {
                        logger(ERROR, 'api response failed while getting qr code: ' + errorThrown + " - " + data);
                        callback(null);
                        return;
                    });
            } else {
                return {
                    "error": "no secsignid"
                };
            }
        }

        //checks if id does exist on the server
        var requestIdExistsOnServer = function (id, callback) {
            logger(DEBUG, 'request if id exists to ' + settings.restApiEnrollment);
            $.get(OC.generateUrl("/apps/secsignid/exists/"))
                .done(function (data) {
                    console.log(data)
                    logger(DEBUG, 'api response while testing if ID exists: ' + data);
                    if (data.exists === "true" || data.exists === true) {
                        $('.secUi-pageAccesspass__accesspass').prop("src", "data:image/png;base64," + data.session.authsessionicondata);
                        $("#secUi-pageAccesspass_session").val(JSON.stringify(data.session));
                        callback(true);
                        return;
                    }
                    if (data.exists === "false" || data.exists === false) {
                        callback(false);
                        return;
                    }
                    logger(ERROR, 'unknown api response while testing if ID exists: ' + data);
                    callback(null);
                    return;
                })
                .fail(function (jqXHR, textStatus, errorThrown, data) {
                    logger(ERROR, 'api response failed while testing if ID exists: ' + errorThrown + " - " + data);
                    callback(null);
                    return;
                });
        };
        var requestIdExistsOnServerPolling = function (id, callback) {
            requestIdExistsOnServer(id, function (idexists) {
                if (idexists === true) {
                    setAppState('pageAccesspass');
                } else if (idexists === false) {
                    pollingEnrollmentExist = window.setTimeout(function () {
                        requestIdExistsOnServerPolling(id, function (idexists) {});
                    }, settings.pollingInterval);
                } else {
                    setError(2001);
                }
            });
        }

        //checks if id was restored on server
        var requestIdRestoredOnServer = function (id, callback) {
            logger(DEBUG, 'request if id is restored to ' + settings.restApiEnrollment);
            $.post(settings.restApiEnrollment, {
                    "appstate": "enrollment_idrestored",
                    "secsignid": id
                })
                .done(function (data) {
                    logger(DEBUG, 'api response while testing if ID already restored: ' + data);
                    if (data == "true") {
                        callback(true);
                        return;
                    }
                    if (data == "false") {
                        callback(false);
                        return;
                    }
                    logger(ERROR, 'unknown api response while testing if ID was restored: ' + data);
                    callback(null);
                    return;
                })
                .fail(function (jqXHR, textStatus, errorThrown, data) {
                    logger(ERROR, 'api response failed while testing if ID restored: ' + errorThrown + " - " + data);
                    callback(null);
                    return;
                });
        };
        var requestIdRestoredOnServerPolling = function (id, callback) {
            requestIdRestoredOnServer(id, function (idrestored) {
                if (idrestored === true) {
                    setAppState('pageAccesspass');
                } else if (idrestored === false) {
                    pollingEnrollmentRestored = window.setTimeout(function () {
                        requestIdRestoredOnServerPolling(id, function (idrestored) {});
                    }, settings.pollingInterval);
                } else {
                    setError(2001);
                }
            });
        }

        //ENROLLMENT FUNCTIONS END

        //----------------------------

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

            //remove unused pages
            if (settings.enrollmentCustomIdAllowed) {
                $('#secUi-pageApps__newIDbtn').remove();
            } else {
                logger(DEBUG, 'switch off enrollment with custom ID');
                $('#secUi-pageExistingID').remove();
                $('#secUi-pageApps__existingIDbtn').remove();
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
                '</style>'
            ).appendTo('head');
            logger(DEBUG, 'set up custom color');

            //set up header logo & service name
            $('.secUi-header__companyname').text(settings.serviceName);

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
            window.clearTimeout(pollingEnrollmentExist);
            window.clearTimeout(pollingEnrollmentRestored);
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
                    $("#secUi-pageAccesspass__accesspassicon").show();
                    pollingAuthSessionState =  checkAuthSessionStateFunc();
                    console.log(settings);
                    loader(false, function () {});
                    //has loader in callback
                    //requestAuthSession();
                    break;

                case "cancelAuth":
                    setProgress(10);
                    cancelAuthSession();
                    $('.secUi-page').hide();
                    $('#secUi-' + initialAppstate).show();
                    loader(false, function () {});
                    break;

                    //enrollment related appstates

                case "pageApps":
                    setProgress(10);
                    $('.secUi-page').hide();
                    $('#secUi-pageApps').show();
                    loader(false, function () {});
                    break;

                case "prepareQr":
                    setProgress(50);
                    //check if there is an ID otherwise error
                    console.log(settings);
                    if (settings.userSecSignId == null || settings.userSecSignId == "") {
                        setError(2001);
                        return;
                    }
                    $('.secUi-main__displayid').text(settings.userSecSignId);
                    $('.secUi-main__displayemail').text(settings.userEmail);
                    //ask server if ID exists already
                    requestIdExistsOnServer(settings.userSecSignId, function (idexists) {
                        if (idexists == true) {
                            //id exists already, claim it info page
                            setAppState('pageClaim');
                            return;
                        } else if (idexists == null) {
                            setError(2006);
                            return;
                        }

                        if (settings.enrollmentMode == QRSIMPLE) {
                            //ask server for a simple qr Code
                            $('.secUi-pageQr__code').attr('src', OC.generateUrl("/apps/secsignid/qr/"));
                            setAppState('pageQrSimple');

                            //set up polling for SecSignId existance during enrollment
                            requestIdExistsOnServerPolling(settings.userSecSignId, function (idexists) {});
                            return;
                        } else if (settings.enrollmentMode == QRRESTORE) {
                            //ask server for a restore qr Code
                            requestCode({
                                "type": QRRESTORE,
                                "secsignid": settings.userSecSignId,
                                "email": settings.userEmail,
                                "firstname": settings.userFirstname,
                                "lastname": settings.userLastname,
                                "enable": "true",
                            }, function (response) {
                                logger(DEBUG, 'QR code response: ' + response);

                                if (noErrors(response)) {
                                    //display qrcode
                                    if (response.qrcodebase64) {
                                        logger(DEBUG, 'QR code valid, display it');
                                        $('.secUi-pageQrRestore__code').attr('src', 'data:image/png;base64,' + response.qrcodebase64);
                                        setAppState('pageQrRestore');

                                        //set up polling for SecSignId existance during enrollment
                                        requestIdRestoredOnServerPolling(settings.userSecSignId, function (idrestored) {});
                                        return;
                                    } else {
                                        setError(1002);
                                        return;
                                    }
                                }
                            });
                        } else if (settings.enrollmentMode == OTP) {
                            //ask server for a restore qr Code
                            requestCode({
                                "type": OTP,
                                "secsignid": settings.userSecSignId,
                                "email": settings.userEmail
                            }, function (response) {
                                logger(DEBUG, 'QR code response: ' + response);

                                if (noErrors(response)) {
                                    //display otp page

                                }
                            });
                        }
                    });
                    return;

                case "pageQrDesktop":
                    //if called, always after prepareQr so polling etc is also enabled
                    setProgress(50);
                    $('.secUi-page').hide();
                    $('#secUi-pageQrDesktop').show();
                    loader(false, function () {});
                    break;

                case "pageQrSimple":
                    //always called after prepareQr so polling etc is enabled
                    setProgress(50);
                    $('.secUi-page').hide();
                    $('#secUi-pageQr').show();
                    loader(false, function () {});
                    break;

                case "pageQrRestore":
                    //always called after prepareQr so polling etc is enabled
                    setProgress(50);
                    $('.secUi-page').hide();
                    $('#secUi-pageQrRestore').show();
                    loader(false, function () {});
                    break;

                case "pageClaim":
                    setProgress(30);
                    $('.secUi-page').hide();
                    $('.secUi-main__displayid').text(settings.userSecSignId);
                    $('#secUi-pageClaim').show();
                    loader(false, function () {});
                    break;

                case "pageExistingID":
                    setProgress(20);
                    $('.secUi-page').hide();
                    $('#secUi-pageExistingID').show();
                    $('#secUi-main__newId').focus();
                    loader(false, function () {});
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

        //create new ID button points to QR code generation
        $('#secUi-main__container').on('click', '#secUi-pageApps__newIDbtn', function () {
            loader(true, function () {
                setAppState('prepareQr');
            });
        });

        //use existing ID form points to QR code generation
        $('#secUi-main__container').on('click', '#secUi-pageExistingID__newbtn', function () {
            if ($('#secUi-main__newId').val()) {
                loader(true, function () {
                    settings.userSecSignId = $('#secUi-main__newId').val();
                    setAppState("prepareQr");
                });
            } else {
                //hint?
            }
        });

        //button to use existing ID points to designated page
        $('#secUi-main__container').on('click', '#secUi-pageApps__existingIDbtn', function () {
            loader(true, function () {
                setAppState('prepareQr');
            });
        });

        //button to show no QR for desktop apps points to manual/link page
        $('#secUi-main__container').on('click', '#secUi-pageQr__desktopbtn', function () {
            loader(true, function () {
                setAppState('pageQrDesktop');
            });
        });

        //button to go back to QR code points to QR code page
        $('#secUi-main__container').on('click', '#secUi-pageQrDesktop__appbtn', function () {
            loader(true, function () {
                setAppState('pageQrSimple');
            });
        });

        //button to show no QR for desktop apps points to manual/link restore page
        $('#secUi-main__container').on('click', '#secUi-pageQrRestore__desktopbtn', function () {
            //    loader(true, function(){
            //        setAppState('pageQrRestoreDesktop');
            //    });
        });

        //button to go back to QR code points to QR restore page
        $('#secUi-main__container').on('click', '#secUi-pageQrRestoreDesktop__appbtn', function () {
            loader(true, function () {
                setAppState('pageQrRestore');
            });
        });

        //button to confirm claimingof an ID starts authentication and points to accesspass page
        $('#secUi-main__container').on('click', '#secUi-pageClaim__toauthbtn', function () {
            loader(true, function () {
                setAppState('pageAccesspass');
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

        //public functions
        return {

            //start enrollment and set up UI
            startApp: function (appstate, error) {

                //save initial appstate
                initialAppstate = appstate;

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
}(OC, jQuery));