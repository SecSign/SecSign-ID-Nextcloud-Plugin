<!--
- @author SecSign Technologies Inc.
- @copyright 2019 SecSign Technologies Inc.
-->
<?php
script('secsignid','SecSignIDUi_Enrollment');
script('secsignid','onboarding_script');
style('secsignid','SecSignIDUi');
session_start();
?>

<html>
    <body>

<!-- Start SecSignID HTML Template -->

        <div id="secUi-main__container">
            <div class="secUi-main__wrapper">

                <div class="secUi-header">
                    <img class="secUi-header__logo">

                    <div class="secUi-header__container">
                        <div class="secUi-header__heading">
                            <span class="secUi-header__companyname">NextCloud</span> <span class="secUi-custcolor">Signup</span>
                        </div>
                        <div class="secUi-header__subheading">
                            <span class="secUi-header__username"><?php p($username); ?></span> Please activate 2FA for your account
                        </div>
                    </div>
                </div>

                <div class="secUi-progress">
                    <div class="secUi-progress__bar secUi-custbgcolor"></div>
                </div>

<div id="secUi-main__pageContainer">
                <div id="secUi-pagePw" class="secUi-page"></div>

<!-- Authentication Pages -->
    <!-- Authentication Page for user credentials 2step (1) -->
                <div id="secUi-pageUserCredentials" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#newexisting" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Insert your user name and password
                    <p>
                    <form id="secUi-main__pwform" action="" method="post">
                        <input type="text" name="secUi-main__userName" id="secUi-main__userName" class="secUi-main__textinput" placeholder="user name">
                        <input type="password" name="secUi-main__userPassword" id="secUi-main__userPassword" class="secUi-main__textinput" placeholder="password">
                        <input type="hidden" id="secUi-main__appstate" name="appstate" value="authentication_pw">
                        <input type="submit" class="secUi-main__button secUi-custbutton" id="secUi-pageUserCreds__nextbtn" value="Next">
                    </form>
                </div>

<!-- Enrollment Page for downloading apps (1) -->
                <div id="secUi-pageApps" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/try-secsign-id-now/" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Please download and install the SecSignID App from one of the following App stores
                    <p>

                    <div class="secUi-pageApps__appcontainer">
                        <a href="https://itunes.apple.com/us/app/secsign-id/id581467871?mt=8" target="_blank" class="secUi-pageApps__appbtn secUi-custappcolor">
                            <svg class="secUi-pageApps__appbtnicon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" viewBox="0 0 20 20">
                                <path d="M17.5640259,13.8623047c-0.4133301,0.9155273-0.6115723,1.3251343-1.1437988,2.1346436c-0.7424927,1.1303711-1.7894897,2.5380249-3.086853,2.5500488
                                    c-1.1524048,0.0109253-1.4483032-0.749939-3.0129395-0.741333c-1.5640259,0.008606-1.8909302,0.755127-3.0438843,0.7442017
                                    c-1.296814-0.0120239-2.2891235-1.2833252-3.0321655-2.4136963c-2.0770874-3.1607666-2.2941895-6.8709106-1.0131836-8.8428955
                                    c0.9106445-1.4013062,2.3466187-2.2217407,3.6970215-2.2217407c1.375,0,2.239502,0.7539673,3.3761597,0.7539673
                                    c1.1028442,0,1.7749023-0.755127,3.3641357-0.755127c1.201416,0,2.4744263,0.6542969,3.3816528,1.7846069
                                    C14.0778809,8.4837646,14.5608521,12.7279663,17.5640259,13.8623047z M12.4625244,3.8076782
                                    c0.5775146-0.741333,1.0163574-1.7880859,0.8571167-2.857666c-0.9436035,0.0653076-2.0470581,0.6651611-2.6912842,1.4477539	C10.0437012,3.107605,9.56073,4.1605835,9.7486572,5.1849365C10.7787476,5.2164917,11.8443604,4.6011963,12.4625244,3.8076782z"/>
                            </svg>
                            iOS App Store
                        </a>

                        <a href="https://play.google.com/store/apps/details?id=com.secsign.secsignid&hl=en" target="_blank" class="secUi-pageApps__appbtn secUi-custappcolor">
                            <svg class="secUi-pageApps__appbtnicon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" viewBox="0 0 20 20">
                                <path d="M4.942627,18.0508423l7.6660156-4.3273926l-1.6452026-1.8234253L4.942627,18.0508423z M2.1422119,2.1231079
                                    C2.0543823,2.281311,2,2.4631958,2,2.664917v15.1259766c0,0.2799683,0.1046143,0.5202026,0.2631226,0.710144l7.6265259-7.7912598
                                    L2.1422119,2.1231079z M17.4795532,9.4819336l-2.6724854-1.508606l-2.72229,2.7811279l1.9516602,2.1630249l3.4431152-1.9436035
                                    C17.7927856,10.8155518,17.9656372,10.5287476,18,10.2279053C17.9656372,9.927063,17.7927856,9.6402588,17.4795532,9.4819336z
                                    M13.3649292,7.1592407L4.1452026,1.954834l6.8656616,7.609314L13.3649292,7.1592407z"></path>
                            </svg>
                            Google Play
                        </a>

                        <a href="https://itunes.apple.com/de/app/secsign-id/id1038409057?l=en&mt=12" target="_blank" class="secUi-pageApps__appbtn secUi-custappcolor">
                            <svg class="secUi-pageApps__appbtnicon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" viewBox="0 0 20 20">
                                <path d="M17.5640259,13.8623047c-0.4133301,0.9155273-0.6115723,1.3251343-1.1437988,2.1346436c-0.7424927,1.1303711-1.7894897,2.5380249-3.086853,2.5500488
                                    c-1.1524048,0.0109253-1.4483032-0.749939-3.0129395-0.741333c-1.5640259,0.008606-1.8909302,0.755127-3.0438843,0.7442017
                                    c-1.296814-0.0120239-2.2891235-1.2833252-3.0321655-2.4136963c-2.0770874-3.1607666-2.2941895-6.8709106-1.0131836-8.8428955
                                    c0.9106445-1.4013062,2.3466187-2.2217407,3.6970215-2.2217407c1.375,0,2.239502,0.7539673,3.3761597,0.7539673
                                    c1.1028442,0,1.7749023-0.755127,3.3641357-0.755127c1.201416,0,2.4744263,0.6542969,3.3816528,1.7846069
                                    C14.0778809,8.4837646,14.5608521,12.7279663,17.5640259,13.8623047z M12.4625244,3.8076782
                                    c0.5775146-0.741333,1.0163574-1.7880859,0.8571167-2.857666c-0.9436035,0.0653076-2.0470581,0.6651611-2.6912842,1.4477539	C10.0437012,3.107605,9.56073,4.1605835,9.7486572,5.1849365C10.7787476,5.2164917,11.8443604,4.6011963,12.4625244,3.8076782z"/>
                            </svg>
                            OSX App Store
                        </a>

                        <a href="https://www.microsoft.com/en-us/p/secsign-id/9nblggh5kq3x" target="_blank" class="secUi-pageApps__appbtn secUi-custappcolor">
                            <svg class="secUi-pageApps__appbtnicon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" viewBox="0 0 20 20">
                                <path d="M9.5,3.2410278V9.5H18V2L9.5,3.2410278z M2,9.5h6.5V3.3870239L2,4.3359985V9.5z M9.5,16.7589722L18,18v-7.5H9.5V16.7589722z
                	               M2,15.6640015l6.5,0.9489746V10.5H2V15.6640015z"></path>
                            </svg>
                            Windows Store
                        </a>
                    </div>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageApps__newIDbtn">Create new SecSignID</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageApps__existingIDbtn">Assign SecSignID</button>
                </div>


<!-- Enrollment Page for displaying QR Code Create with polling (2) -->
                <div id="secUi-pageQr" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/try-secsign-id-now/" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Open the SecSignID app and click/tap on <strong>+</strong>, then on <strong>start QR-Code pairing</strong> and scan the following code
                    <p>
                    <img class="secUi-pageQr__code">
                    <p class="secUi-main__text">
                        This screen will proceed automatically after creating your SecSignID: <strong class="secUi-main__displayid"></strong>
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQr__desktopbtn">No camera / desktop apps</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQr__cancelbtn">Back</button>
                </div>

<!-- Enrollment Page for displaying QR Code Restore with polling (2a) -->
                <div id="secUi-pageQrRestore" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#qrrestore" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Open the SecSignID app and click/tap on <strong>+</strong>, then on <strong>start QR-Code pairing</strong> and scan the following code
                    <p>
                    <img class="secUi-pageQrRestore__code">
                    <p class="secUi-main__text">
                        This screen will proceed automatically after creating your SecSignID: <strong class="secUi-main__displayid"></strong> and enter the email confirmation code sent to <strong class="secUi-main__displayemail"></strong>
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQrRestore__desktopbtn">No camera / desktop apps</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQrRestore__cancelbtn">Back</button>
                </div>


<!-- Enrollment Page for showing manual enrollment for desktop apps (3) -->
                <div id="secUi-pageQrDesktop" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/try-secsign-id-now/" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        <a href="#" class="secUi-pageQr__link secUi-main__button secUi-custbutton">Create your new ID on <strong>this</strong> device</a>
                    <p>
                    <p class="secUi-main__text">
                        To create your ID manually on another device, open the SecSignID app and perform the following steps
                    <p>
                    <ol>
                        <li>Click on <strong>More</strong> tab and choose <strong>New identity on other server</strong></li>
                        <li>Insert the server address: <strong id="secUi-pageQrDesktop__serveraddress"><?php p($GLOBALS['mobile_url']);?></strong></li>
                        <li>Click on next</li>
                        <li>Insert your new SecSignID: <strong class="secUi-main__displayid"></strong></li>
                        <li>Click on next, your new SecSignID will be set up.</li>
                    </ol>
                    <p class="secUi-main__text">
                        This screen will proceed automatically after ID creation
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQrDesktop__appbtn">Back to the QR-Code</button>
                </div>

<!-- Enrollment Page for showing manual enrollment for desktop apps for restore (3) -->
                <div id="secUi-pageQrRestoreDesktop" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#qrrestoredesktop" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        <a href="#" class="secUi-pageQrRestore__link secUi-main__button secUi-custbutton">Create your new ID on <strong>this</strong> device</a>
                    <p>
                    <p class="secUi-main__text">
                        To create your ID manually on another device, open the SecSignID app and perform the following steps
                    <p>
                    <ol>
                        <li>Click on <strong>...more</strong> and choose <strong>Restore ID on other server</strong></li>
                        <li>Insert the server address: <strong id="secUi-pageQrRestoreDesktop__serveraddress"><?php echo $host; ?></strong></li>
                        <li>Click on next</li>
                        <li>Insert your new restore Code: <strong class="secUi-pageQrRestoreDesktop__restorecode"></strong></li>
                        <li>Click on next and enter the email confirmation code, you just received</li>
                        <li>Click on next, your new SecSignID will be set up.</li>
                    </ol>
                    <p class="secUi-main__text">
                        This screen will proceed automatically after ID creation
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQrRestoreDesktop__appbtn">Back to the QR-Code</button>
                </div>


<!-- Enrollment Page for assigning an existing ID (4) -->
                <div id="secUi-pageExistingID" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/try-secsign-id-now/" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Insert your existing SecSignID or create a new one to assign to your user account
                    <p>
                    <input type="text" name="secUi-main__newId" id="secUi-main__newId" class="secUi-main__textinput" placeholder="enter SecSignID">
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageExistingID__newbtn">Next</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageExistingID__cancelbtn">Cancel</button>
                </div>

<!-- Enrollment Page for claiming existing ID (5) -->
                <div id="secUi-pageClaim" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/try-secsign-id-now/" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        If the SecSignID <strong class="secUi-main__displayid"></strong> is yours and you have it already set up on your device, click next. The authentication will start immediately.
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageClaim__toauthbtn">Yes it's mine, go on</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageClaim__cancelbtn">Cancel</button>
                </div>

<!-- Enrollment Page for entering Email address (6) -->
                <div id="secUi-pageEnterEmail" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#enteremail" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                </div>


<!-- Enrollment Page for Email confirmation with polling (7) -->
                <div id="secUi-pageEmailConfirm" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#confirmemail" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                </div>

<!-- Enrollment Page for Errors (1) -->
                <div id="secUi-pageError" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/developers/frequently-asked-questions/" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-pageError__errorCodeContainer">Error #<span id="secUi-pageError__errorCode"></span></p>
                    <p id="secUi-pageError__errorMsg"></p>

                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageError__cancelbtn">Cancel</button>
                </div>

<!-- Authentication Page for Accesspass (2) -->
                <div id="secUi-pageAccesspass" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://www.secsign.com/try-secsign-id-now/" class="secUi-main__helplink secUi-custcolor">Help</a></div>

                    <div id="secUi-pageAccesspass__noaccesspassicon">
                        <p class="secUi-main__text">
                             Access for <strong class="secUi-main__displayid"></strong>
                        <p>
                        <!--<img class="" id="" src="notification.gif">-->
                        <p class="secUi-main__textsmall">
                            Please tap on <strong>Accept</strong><br>in your SecSignID app
                        </p>
                    </div>
                    <div id="secUi-pageAccesspass__accesspassicon">
                        <p class="secUi-main__text">
                             Access pass for <strong class="secUi-main__displayid"></strong>
                        <p>
                        <div class="secUi-pageAccesspass__apcontainer">
                            <img class="secUi-pageAccesspass__accesspass" id="secUi-pageAccesspass__accesspass" src="">
                        </div>
                        <p class="secUi-main__textsmall">
                            Please select the right AccessPass<br>in your SecSignID app
                        </p>
                    </div>

                    <button style='display: none' class="secUi-main__button secUi-custbutton" id="secUi-pageAccesspass__cancelbtn">Cancel</button>
                </div>

<!-- About Page (1) -->
                <div id="secUi-pageAbout" class="secUi-page">
                    <a class="secUi-main__logoicon" target="_blank" href="https://secsign.com"></a>
                    <h3>SecSign Technologies</h3>
                    <p class="secUi-main__text">
                        This Login is protected by SecSign Two Factor Authentication.
                    <p>

                    <p class="secUi-main__textsmall">
                        v 1.3.6
                    </p>
                    <a  class="secUi-main__button secUi-custbutton" href="https://www.secsign.com/" target="_blank">https://www.secsign.com/</a>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageAbout__gobackbtn">Go Back</button>
                </div>

<!-- Loading Page (1) -->
                <div id="secUi-pageLoad" class="secUi-custcolor">
                    <div id="secUi-pageLoad__loaderContainer">
                        <div class="secUi-main__barload">
                            <div class="secUi-custbgcolor"></div>
                            <div class="secUi-custbgcolor"></div>
                            <div class="secUi-custbgcolor"></div>
                        </div>
                        LOADING
                    </div>
                </div>


</div>

                <form id="secUi-main__loginform" action="" method="post">
                    <input id="secUi-pageAccesspass_session" type="hidden" name="challenge">
                    <input type="hidden" id="secUi-main__appstate" name="appstate" value="enrollment_done">
                    <input type="hidden" id="secUi-main__authsessionid" name="authsessionid" value="">
                    <input type="hidden" id="secUi-main__servicename" name="servicename" value="">
                    <input type="hidden" id="secUi-main__secsignid" name="secsignid" value="">
                    <input type="hidden" id="secUi-main__serviceaddress" name="serviceaddress" value="">
                    <input type="hidden" id="secUi-main__authsessionstate" name="authsessionstate" value="">
                    <input type="hidden" id="secUi-main__requestid" name="requestid" value="">
                    <input type="hidden" id="secUi-main__authenticationKey" name="authenticationKey" value="$">
                </form>

            </div>
        </div>

<!-- End SecSignID HTML Template -->
    </body>
</html>
