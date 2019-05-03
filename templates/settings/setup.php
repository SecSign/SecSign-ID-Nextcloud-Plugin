<!--
- @author SecSign Technologies Inc.
- @copyright 2019 SecSign Technologies Inc.
-->
<?php
script('secsignid','confirmation_script');
script('secsignid', 'SecSignIDUi_Confirmation');
style('secsignid','lds_roller');
style('secsignid','settings');
style('secsignid', 'SecSignIDUi');
?>

<div class="section" id='sec'>
	<h2> SecSign 2FA </h2>
	<div class="lds-roller">
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
	</div>
	<div id="enabled" hidden>
		<p id="description"> You have already added a SecSign ID protecting your account.</p>
		SecSign ID: <input id="secsignid_input_en" type="text" name="secsignid">
		<button id="change_id" type="button">Update</button>
		<button id="disable" type="button">Disable</button>
		<div></div>
	</div>
	<div id="disabled" hidden>
		<h1> Enable two factor authentication with your SecSign ID </h1>
		<button id="enable_id" type="button">Enable</button>
	</div>
	<div id="noedit_enabled" hidden>
		<h1> You currently have a SecSign ID protecting your account. To change this ID or disable SecSign 2FA, contact your administrator.</h1>
		<h1 style="margin-top=8px; margin-left=8px ;font-size: 150%" class="id">Secsign ID: </h1>
	</div>
	<div id="noedit_disabled" hidden>
		<h1> SecSign 2FA is currently not set up for this account. Contact your administrator for more information.</h1>
	</div>
	<div id="myModal" class="modal">
        <div id="secUi-main__container" style="margin-top: 100px">
            <div class="secUi-main__wrapper">

                <div class="secUi-header">
                    <img class="secUi-header__logo">

                    <div class="secUi-header__container">
                        <div class="secUi-header__heading">
                            <span class="secUi-header__companyname">Nextcloud</span> <span class="secUi-custcolor">2FA</span>
                        </div>
                        <div class="secUi-header__subheading">
                            Assign a SecSign ID
                        </div>
                    </div>
                </div>

                <div class="secUi-progress">
                    <div class="secUi-progress__bar secUi-custbgcolor"></div>
                </div>

<div id="secUi-main__pageContainer">
                <div id="secUi-pagePw" class="secUi-page"></div>

<!-- Authentication Page for user credentials 2step (1) -->

                <!-- Enrollment Page for assigning an existing ID (4) -->
                <div id="secUi-pageExistingID" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#newexisting" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Insert your existing SecSignID or create a new one to assign to your user account
                    <p>
                    <input type="text" name="secUi-main__newId" id="secUi-main__newId" class="secUi-main__textinput" placeholder="enter SecSignID">
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageExistingID__newbtn">Next</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageExistingID__cancelbtn">Cancel</button>
                </div>

                <!-- Enrollment Page for displaying QR Code Create with polling (2) -->
                <div id="secUi-pageQr" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#qrapp" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        Open the SecSignID app and click/tap on <strong>+</strong>, then on <strong>start QR-Code pairing</strong> and scan the following code
                    <p>
                    <img class="secUi-pageQr__code">
                    <p class="secUi-main__text">
                        This screen will proceed automatically after creating your SecSignID: <strong class="secUi-main__displayid"></strong>
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQr__desktopbtn">No camera / desktop apps</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageQr__cancelbtn">Cancel</button>
                </div>

<!-- Enrollment Page for claiming existing ID (5) -->
                <div id="secUi-pageClaim" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#claim" class="secUi-main__helplink secUi-custcolor">Help</a></div>
                    <p class="secUi-main__text">
                        If the SecSignID <strong class="secUi-main__displayid"></strong> is yours and you have it already set up on your device, click next. The authentication will start immediately.
                    <p>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageClaim__toauthbtn">Yes it's mine, go on</button>
                    <button class="secUi-main__button secUi-custbutton" id="secUi-pageClaim__cancelbtn">Cancel</button>
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

                <div id="secUi-pageQrDesktop" class="secUi-page">
                    <div class="secUi-main__helpcontainer"><a target="_blank" href="https://secsign.com/help#qrdesktop" class="secUi-main__helplink secUi-custcolor">Help</a></div>
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
	</div>
		
</div>