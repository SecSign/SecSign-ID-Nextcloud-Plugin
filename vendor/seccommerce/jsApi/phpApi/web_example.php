<?php
	//
	//
	// a simple demo how to use the SecSignIDApi interface.
	//
	// (c) 2012 - 2016 SecSign Technologies Inc.
	//
	//
	
    include ('SecSignIDApi.php');
    
    echo "<html><head><title>SecSign ID Api example</title></head>" . PHP_EOL . PHP_EOL;
    echo "<body>". PHP_EOL. PHP_EOL;
    
    echo "<h1>SecSign ID Api example</h1>" . PHP_EOL;
    echo "<br /><br /><br />" . PHP_EOL;
    
    
    //---------------------------------------------------------------------------------
    //
    //
    // step 3: The user hit the 'OK' or 'Cancel' button
    //
    // In posted request a parameter request id and an authsession id is set. in this case the authentication session state shall be checked or canceled
    // to check the authentication state a new api instance needs to be created. get all information about The authentication session which was stored in html fields or in the php session object
    // and create an authentication session object. Afterwards the state can be retrieved from server. Depending of the state
    // the user could be logged in at the cms or system using the SecSign ID Api.
    //
    //
    //
    if(isset($_POST['requestid']) && isset($_POST['authsessionid']))
    {
        // if the request id is set The authentication session status has to be checked or The authentication session has to be canceled. this depends on the request name
        try
        {
            // create a new session instance which is needed to check its status
            $authsession = new AuthSession();
            $authsession->createAuthSessionFromArray(array(
                                       'requestid' => $_POST['requestid'],
                                       'secsignid' => $_POST['secsignid'],
                                       'authsessionid' => $_POST['authsessionid'],
                                       'servicename' => $_POST['servicename'],
                                       'serviceaddress' => $_POST['serviceaddress'],
                                       'authsessionicondata' => $_POST['authsessionicondata']
                                       ));
        
        	// create new secsign id api instance
            $secSignIDApi = new SecSignIDApi();
            
            // the parameter which should be checked depends on the name you have given the 'OK' button. see step 2
            if(isset($_POST['check']))
            {
				// get the authentication session state
                $authSessionState = $secSignIDApi->getAuthSessionState($authsession);
            
                //---------------------------------------------------------------------------------
                //
                //
                // The authentication session has been accepted. this is the only case where the web-application or cms can login the user in the underlying system.
                // session might be set and everything which is needed.
                //
                //
                //
                if($authSessionState == AuthSession::AUTHENTICATED)
                {                    
                    // user accepted the authentication session...
                    echo "Welcome " . $_POST['secsignid'] . PHP_EOL;
                    echo "<br /><br />";
                    echo "You have been logged in to your account... <br />" . PHP_EOL;
                }
                
                
                
                //---------------------------------------------------------------------------------
                //
                //
                // the user has denied The authentication session. guessing he didnt want to login
                //
                //
                //
                else if($authSessionState == AuthSession::DENIED)
                {
                    echo "You have denied The authentication session..." . PHP_EOL;
                    echo "<br /><br />";
                    
                    printLoginForm();
                }

                
                
                //---------------------------------------------------------------------------------
                //
                //
                // The authentication session is still pending. the user didnt accept or denied The authentication session.
                // The authentication session is in the state fetched if the user got all access pass icons on his smart phone: the session was fetched by the user on his smartphone.
                //
                //
                //
                else if ($authSessionState == AuthSession::PENDING || $authSessionState == AuthSession::FETCHED)
                {
                    echo "The authentication session is still pending... It has neither be accepted nor denied." . PHP_EOL;
                    echo "<br /><br />";
                    
                    // print access pass, hidden fields and the form to submit check of authentication state
                    printAccessPassForm($authsession);
                }
                
                
                
                //---------------------------------------------------------------------------------
                //
                //
                // The authentication session might be expired or something else happend...
                //
                //
                //
                else
                {
                    echo "The authentication session has an unknown status " . $authSessionState . ". therefore you cannot be logged in..." . PHP_EOL;
                    echo "<br /><br />";
	                printLoginForm();
                }
            }
            else
            {
                // user hit cancel. in fact in this example there is no cancel button
                $secSignIDApi->cancelAuthSession($authsession);
                
                echo "You have canceled the login process..." . PHP_EOL;
                echo "<br /><br />";
                printLoginForm();
            }
        }
        catch(Exception $e)
        {
            echo "An error occured when getting authentication session status : " . $e->getMessage() . PHP_EOL;
            
            echo "<br /><br />";
            printLoginForm();
        }
    }
    
    
    //---------------------------------------------------------------------------------
    //
    //
    // step 2: the user entered his secsign id and hit the 'Login' button
    //
    // An authentication session was received (otherwise an exception will be thrown)
    // All data which is needed to check its state must be saved. This can be done by saving values in hidden input fields or at the php session or data could be written to db or files
    // The data which is used:
	//
	// $authsession->getRequestID()
	// $authsession->getSecSignID()
	// $authsession->getAuthSessionID()
	// $authsession->getRequestingServiceName()
	// $authsession->getRequestingServiceAddress()
	// $authsession->getIconData()
    //
    //
    else if(isset($_POST['secsignid']) && isset($_POST['login']))
    {
        // contact secsign id server and request authentication session
        try
        {
            $secSignIDApi = new SecSignIDApi();
            $authsession = $secSignIDApi->requestAuthSession($_POST['secsignid'], 'web example how to use SecSignIDApi', $_SERVER['SERVER_NAME']);
            
            if(isset($authsession))
            {    
                // print access pass, hidden fields and the form to submit check of authentication state
                printAccessPassForm($authsession);
            }
        }
        catch(Exception $e)
        {
            echo "An error occured when requesting the authentication session : " . $e->getMessage() . PHP_EOL;
            
            echo "<br /><br />";
            printLoginForm();
        }
    }
    
    
    
    //---------------------------------------------------------------------------------
    //
    //
    // step 1: print textfield for secsign id and a 'Login' submit button
    //
    //
    //
    else
    {
        printLoginForm();
    }
    
    // print ending of html page...
    echo "</body>". PHP_EOL. PHP_EOL;
    echo "</html>". PHP_EOL. PHP_EOL;
    
    
    
    
    
    
    //---------------------------------------------------------------------------------
    //
    //
    // methods to print html forms and the access pass with given icon data
    //
    //
    //
    function printLoginForm()
    {
        echo "<form action='web_example.php' method='post'>" . PHP_EOL;
        echo "SecSign ID: <input id='secsignid' name='secsignid' type='text' size='30' maxlength='30' />" . PHP_EOL;
        echo "<button type ='submit' name='login' value='1'>Login</button> <br />" . PHP_EOL;
        echo "</form>";
    }
    
    function printAccessPassForm($authsession)
    {
     	// show image data, print all information which is need to verify authentication session
        echo "<form action='web_example.php' method='post'>" . PHP_EOL;
                    
        // all information which is need to get session status if user hit 'OK' button
        printHiddenFormFields($authsession);
        
        // print a nice html-table with a access pass
        printCheckAccessPass($authsession->getIconData());
                    
        // end of form
        echo "</form>". PHP_EOL;
    }
    
    function printHiddenFormFields($authsession)
    {
    	echo "<input type='hidden' name='requestid' value='" . $authsession->getRequestID() . "' />" . PHP_EOL;
        echo "<input type='hidden' name='secsignid' value='" . $authsession->getSecSignID() . "' />" . PHP_EOL;
        echo "<input type='hidden' name='authsessionid' value='" . $authsession->getAuthSessionID() . "' />" . PHP_EOL;
        echo "<input type='hidden' name='servicename' value='" . $authsession->getRequestingServiceName() . "' />" . PHP_EOL;
        echo "<input type='hidden' name='serviceaddress' value='" . $authsession->getRequestingServiceAddress() . "' />" . PHP_EOL;
        echo "<input type='hidden' name='authsessionicondata' value='" . $authsession->getIconData() . "' />" . PHP_EOL;
    }
    
    function printCheckAccessPass($iconData)
    {
        echo "<table>" . PHP_EOL;
        echo "<tr>" . PHP_EOL;
        echo "<td colspan='2'>" . PHP_EOL;
        echo "Please verify the access pass using your smartphone.<br>Choose the correct one by tapping on it. After that please click OK: <br /><br/>" . PHP_EOL;
        echo "</td>" . PHP_EOL;
        echo "</tr>" . PHP_EOL;
        echo "<tr>" . PHP_EOL;
        echo "<td colspan='2'>" . PHP_EOL;
        echo "<div style='background-color:#98bde2;color:#FFF;padding:10px;margin:10px;font-size:1.6em;text-align:center;'>" . PHP_EOL;
        echo "<img src=\"data:image/png;base64," . $iconData . "\">" . PHP_EOL;
        echo "</div><br /><br />" . PHP_EOL;
        echo "</td>" . PHP_EOL;
        echo "</tr>" . PHP_EOL;
        echo "<tr>" . PHP_EOL;
        echo "<td align='left'>" . PHP_EOL;
        echo "<button type ='submit' name='cancel' value='1' style='width:100px'>Cancel</button>" . PHP_EOL;
        echo "</td>" . PHP_EOL;
        echo "<td align='right'>" . PHP_EOL;
        echo "<button type ='submit' name='check' value='1' style='width:100px'>OK</button>" . PHP_EOL;
        echo "</td>" . PHP_EOL;
        echo "</tr>" . PHP_EOL;
        echo "</table>" . PHP_EOL;
    }

?>
