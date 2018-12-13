<?php
//
// SecSign ID Api example in php.
//
// (c) 2014-2018 SecSign Technologies Inc.
//

	include 'SecSignIDApi.php';
    
    function logFromSecSignIDApi($message)
    {
        //$log = &JLog::getInstance('secsignid.log');
        //$log->addEntry(array('comment' => 'SecSignIDApi: ' . $message));
        echo $message . PHP_EOL;
    };

	//
	//
	// Example how to retrieve an authentication session, ask its status and withdraw the authentication session.
    //
    //	
    
    //
    // Create an instance of SecSignIDApi.
    //
    echo "create new instance of SecSignIDApi." . PHP_EOL;
	$secSignIDApi = new SecSignIDApi();
                                                  
    //
    // If extended logging is wished set a reference to a function (or the name of a function). 
    // All messages will be given as parameter to this function. 
    // If the function is callable this will be used to log messages
    //
    $secSignIDApi->setLogger('logFromSecSignIDApi');


    //
    // The servicename and address is mandatory. It has to be send to the server.
    // The value of $servicename will be shown on the display of the smartphone. The user then can decide whether he accepts the authentication session shown on his mobile phone.
    //
    $servicename = "Your Website Login";
    $serviceaddress = "http://www.yoursite.com/";
    $secsignid = "username";
    
    //
    // Get a auth session for the sepcified SecSign ID
    //
    // If $secsignid is null or empty an exception is thrown.
    // If $servicename is null or empty an exception is thrown.
    //
    try
    {
        $authSession = $secSignIDApi->requestAuthSession($secsignid, $servicename, $serviceaddress);
        echo "got authSession '" . $authSession . "'" . PHP_EOL;
    }
    catch(Exception $e)
    {
        echo "could not get an authentication session for SecSign ID '" . $secsignid . "' : " . $e->getMessage() . PHP_EOL;
        exit();
    }
    
    //
    // Get the auth session status
    //
    // If $authSession is null or not an instance of AuthSession an exception is thrown
    //
    $authSessionState = AuthSession::NOSTATE;
    
    try
    {
        $authSessionState = $secSignIDApi->getAuthSessionState($authSession);
        echo "got auth session state: " . $authSessionState . PHP_EOL;
    }
    catch(Exception $e)
    {
        echo "could not get status for authentication session '" . $authSession->getAuthSessionID() . "' : " . $e->getMessage() . PHP_EOL;
        exit();
    }
    
    
    
    // If the script shall wait till the user has accepted the auth session or denied it,  it has to ask the server frequently
    $secondsToWaitUntilNextCheck = 10;
    $noError = TRUE;
	
    while(($authSessionState == AuthSession::PENDING || $authSessionState == AuthSession::FETCHED) && $noError)
    {
        try
        {
            $authSessionState = $secSignIDApi->getAuthSessionState($authSession);
            echo "auth session state    : " . $authSessionState . PHP_EOL;
            
            if($authSessionState == AuthSession::PENDING || $authSessionState == AuthSession::FETCHED){
                sleep($secondsToWaitUntilNextCheck);
            }
        } 
        catch (Exception $e) 
        {
            echo "could not get auth session status for auth session '" . $authSession->getAuthSessionID() . "' : " . $e->getMessage() . PHP_EOL;
            $noError = FALSE;
        }
    }
    
    
    if($authSessionState == AuthSession::AUTHENTICATED)
    {
        echo "user has accepted the auth session '" . $authSession->getAuthSessionID() . "'." . PHP_EOL;
    }
    else if($authSessionState == AuthSession::DENIED)
    {
        echo "user has denied the auth session '" . $authSession->getAuthSessionID() . "'." . PHP_EOL;
        $authSessionState = $secSignIDApi->cancelAuthSession($authSession); // after the auth session is successfully canceled it is not possible to inquire the status again
        if($authSessionState == AuthSession::CANCELED)
        {
            echo "authentication session successfully cancelled..." . PHP_EOL;
        }
    }
    else {
        echo "auth session '" . $authSession->getAuthSessionID() . "' has state " . authSessionState . "." . PHP_EOL;
        $authSessionState = $secSignIDApi->cancelAuthSession($authSession); // after the auth session is successfully canceled it is not possible to inquire the status again
        if($authSessionState == AuthSession::CANCELED)
        {
            echo "authentication session successfully cancelled..." . PHP_EOL;
        }
    }
    
    //
    // When using SecSignIDApi.php in a webservice like Joomla, Wordpress or something else
    // it can be necessary to get and check an auth session in two steps.
    // First the auth session is requested. After that all important information has to be stored. In joomla this is done by writing the information to the html output as a hidden field.
    // The html output will show the access pass and ask the user to press a button after he has accepted or denied the auth session on his mobile phone.
    // By pressing the button all information will be send to the server to a script which can use different functions to create an instance of MobileAuth session.
    // Using this instance the secsign id server can be asked for the auth session status. The script then has to decide whether the user is redirected to an internal area or back to the login.
    //
    
    echo PHP_EOL . PHP_EOL . PHP_EOL;
    echo "creating new auth session for secsign id '" . $secsignid . "'." . PHP_EOL;
    
    $secSignIDApi = new SecSignIDApi();
    $authSession = $secSignIDApi->requestAuthSession($secsignid, $servicename, $serviceaddress);
    
    echo "got auth session '" . $authSession . "'." . PHP_EOL;
        
    // The following information are required to request authsession state. These information shall be written to html output, session variables etc.
    $secsignidFromAuthSession = $authSession->getSecSignID();
    $authsessionidFromAuthSession = $authSession->getAuthSessionID();
    $requestidFromAuthSession = $authSession->getRequestID();
    $icondataFromAuthSession = $authSession->getIconData(); // This data is base64 encoded and can be displayed directly in the browser

    $secSignIDApi = null;

    // Build a valid AuthSession instance
    echo "create new AuthSession instance." . PHP_EOL;
    $authSession = new AuthSession();
    $authSession->createAuthSessionFromArray(
                                        array('secsignid'           => $secsignidFromAuthSession,
                                              'authsessionid'		=> $authsessionidFromAuthSession,
                                              'requestid'           => $requestidFromAuthSession,
                                              'servicename'         => $servicename,
                                              'serviceaddress'      => $serviceaddress,
                                              'authsessionicondata' => icondataFromAuthSession));
    
    
    // Create new SecSignIDApi instance
    echo "create new instance of SecSignIDApi." . PHP_EOL;
    $secSignIDApi = new SecSignIDApi();
    
    
    // Ask for auth session state using the newly build AuthSession instance
    echo "get auth session status from server." . PHP_EOL;
    $authSessionState = $secSignIDApi->getAuthSessionState($authSession);
    
    echo "cancel auth session." . PHP_EOL;
    
    // Canceling the ticket here is just to clean up
    $authSessionState = $secSignIDApi->cancelAuthSession($authSession);
    
?>
