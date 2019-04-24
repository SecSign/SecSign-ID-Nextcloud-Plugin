<?php
script('secsignid','SecSignIDUi_Authentication');
script('secsignid','authentication_script');
style('secsignid','SecSignIDUi');
session_start();
?>


<html>
    <body >

<!-- Start SecSignID HTML Template -->

        <div id="secUi-main__container">
            <div class="secUi-main__wrapper">

                <div class="secUi-header">
                    <img class="secUi-header__logo">

                    <div class="secUi-header__container">
                        <div class="secUi-header__heading">
                            <span class="secUi-header__companyname">Nextcloud</span> <span class="secUi-custcolor">Login</span>
                        </div>
                        <div class="secUi-header__subheading">
                            Log into your account
                        </div>
                    </div>
                </div>

                <div class="secUi-progress">
                    <div class="secUi-progress__bar secUi-custbgcolor"></div>
                </div>

<div id="secUi-main__pageContainer">
                <div id="secUi-pagePw" class="secUi-page"></div>

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

<!-- Authentication Page for Errors -->
                <div id="secUi-pageError" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#error" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-pageError__errorCodeContainer">Error #<span id="secUi-pageError__errorCode"></span></p>
                    <p id="secUi-pageError__errorMsg"></p>

                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageError__cancelbtn">Cancel</button>
                </div>

<!-- Authentication Page for Accesspass -->
                <div id="secUi-pageAccesspass" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#accesspass" class="secUi-main__helplink secUi-custcolor">Help</a></div>

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

                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageAccesspass__cancelbtn">Cancel</button>
                </div>

<!-- About Page -->
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

<!-- Loading Page -->
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
                    <input type="hidden" id="secUi-main__appstate" name="appstate" value="pageAccesspass">
                    <input type="hidden" id="secUi-main__authsessionid" name="authsessionid" value="">
                    <input type="hidden" id="secUi-main__servicename" name="servicename" value="">
                    <input type="hidden" id="secUi-main__secsignid" name="secsignid" value="">
                    <input type="hidden" id="secUi-main__serviceaddress" name="serviceaddress" value="">
                    <input type="hidden" id="secUi-main__authsessionstate" name="authsessionstate" value="">
                    <input type="hidden" id="secUi-main__requestid" name="requestid" value="">
                    <input type="hidden" id="secUi-main__authenticationKey" name="authenticationKey" value="">
                </form>

            </div>
        </div>

<!-- End SecSignID HTML Template -->
    </body>
</html>
