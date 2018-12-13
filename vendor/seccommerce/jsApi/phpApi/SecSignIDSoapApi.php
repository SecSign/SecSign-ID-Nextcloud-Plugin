<?php

// $Id: SecSignIDSoapApi.php,v 1.3 2014/12/29 16:56:42 titus Exp $
// $Source: /cvsroot/SecCommerceDev/seccommerce/secsignerid/php/SecSignIDSoapApi.php,v $
// $Log: SecSignIDSoapApi.php,v $
// Revision 1.3  2014/12/29 16:56:42  titus
// Constructor now has only one parameter.
// Added class AuthSession with session state constants.
//
// Revision 1.2  2014/12/18 14:21:55  titus
// Do not merge account data into request array. use account data to use http basic authentication.
//
// Revision 1.1  2014/12/16 16:43:25  titus
// SecSign ID Api using php soap client class and not curl.
//
// Revision 1.1  2014/12/16 14:05:57  titus
// SecPKI-PHP-Api including a small example.
//


define("SECPKI_API_SCRIPT_REVISION", '$Revision: 1.3 $');
    

class AuthSession
{
        /*
         * No State: Used when the session state is undefined. 
         */
        const NOSTATE = 0;
        
        /*
         * Pending: The session is still pending for authentication.
         */
        const PENDING = 1;
        
        /*
         * Expired: The authentication timeout has been exceeded.
         */
        const EXPIRED = 2;
        
        /*
         * Authenticated: The user was successfully authenticated.
         */
        const AUTHENTICATED = 3;
        
        /*
         * Denied: The user denied this session.
         */
        const DENIED = 4;
		
        /*
         * Suspended: The server suspended this session, because another authentication request was received while this session was still pending.
         */
        const SUSPENDED = 5;
        
        /*
         * Canceled: The service has canceled this session.
         */
        const CANCELED = 6;
        
        /*
         * Fetched: The device has already fetched the session, but the session hasn't been authenticated or denied yet.
         */
        const FETCHED = 7;
    
        /*
         * Invalid: This session has become invalid.
         */
        const INVALID = 8;
    }

/**
 * SecSignIDSoapApi: API of the SecCommerce SecPKI server.
 *
 * @version $Id: SecSignIDSoapApi.php,v 1.3 2014/12/29 16:56:42 titus Exp $
 * @author SecCommerce Informationssysteme GmbH, Hamburg
 */
class SecSignIDSoapApi
{
    
	// the secpki connection
	private $secpki;
	
	// account data for a secpki pin account
	private $account;
	
	// once created the api can be used to create a single request for a certain specified userid
    private $secSignIDServer;
    private $secSignIDServerPort;
    private $secSignIDServerUrl;
        
    // numeric script version.
    private $scriptVersion = 0;
    private $referer;
    private $logger;
	
	// soap namespace
	private $ns;
	
	/**
     * Constructor
     */
	/*function __construct($serverAddress = "http://secpkiapi.secsign.com", $serverPort = 25101)
    {
        $this->secSignIDServer     = $serverAddress;
        $this->secSignIDServerPort = $serverPort;
    	$this->__construct($this->secSignIDServer . ":" . $this->secSignIDServerPort);
    }*/
    
    /**
     * Constructor
     */
    function __construct($serverUrl = "http://secpkiapi.secsign.com:25101")
    {
        // server/secpki hostname and port
        $this->secSignIDServerUrl = $serverUrl;
        
        // script version from cvs revision string
        $firstSpace = strpos(SCRIPT_REVISION, " ");
        $lastSpace = strrpos(SCRIPT_REVISION, " ");
        $this->scriptVersion = trim( substr(SCRIPT_REVISION, $firstSpace, $lastSpace-$firstSpace) );

        $this->referer = __CLASS__ . "_PHP";

	    // namespace and wsdl (webservice definition language) information
	    $this->ns = ["prefix" => "secsignid", "uri" => "http://seccommerce.de/ws/SecPKI/", "wsdlurl" => "https://httpapi.secsign.com:25100/"];
		
		// setup soap headers
        $soapHeaders[] = new SoapHeader($this->ns["uri"], $this->ns["prefix"], null);
        $soapHeaders[] = new SoapHeader('http://www.w3.org/2001/XMLSchema', 'xsd');
        $soapHeaders[] = new SoapHeader('http://www.w3.org/2001/XMLSchema-instance', 'xsi');
       
        // prepare soap client/connection to secpki										   
    	$options = array('location' => $this->secSignIDServerUrl,
        				 'uri' => $this->ns["uri"],
        				 'trace' => 1,
    					 'use' => SOAP_LITERAL);
		
		if($this->account != null){
			/*
				Host: localhost:25100
				Connection: Keep-Alive
				User-Agent: PHP-SOAP/5.4.30
				Content-Type: text/xml; charset=utf-8
				SOAPAction: "http://seccommerce.de/ws/SecPKI/#refreshTLSSessionInCache"
				Content-Length: 706
				Authorization: Basic RVhUX1NFQ1JPVVRFUl9BUFA6MTIzNDU2
				X-AuthorizeOrg: U2VjQ29t
			*/
			$options = array_merge($options, array(
        						'login' => $this->account["account"],
						        'password' => $this->account["pin"],
						        
						        "stream_context" => stream_context_create(array("http"=>array(
											        "header"=> "X-AuthorizeOrg: " . base64_encode($this->account["orgShortName"]) . "\r\n"
								)))
		    ));
		}
		
        $this->secpki = new SoapClient($wsdl, $options); 

        $this->secpki->__setSoapHeaders($soapHeaders); 
    }
    
    /**
     * Destructor
     */
    function __destruct(){
    }
	
	/**
     * Encodes the given parameter arraqy into an soap encoded object.
     * It must be called before each request is sent
     */
    private function __encsoap(array $data)
	{
        foreach ($data as &$value) {
			if (is_array($value)) {
            	$value = $this->__encsoap($value);
            }
        }
        return new SoapVar($data, SOAP_ENC_OBJECT);
	}
	
    /**
     * Request an authentication session from ID server
     */
     function requestAuthSession($secSignID, $serviceName = "PHP SecPKI Api", $serviceAddress = "localhost"){
    	$soapDataArray = array("userID" => $secSignID,
    								  "serviceName" => $serviceName,
    								  "serviceAddress" => $serviceAddress,
    								  "browserIpAddr" => $serviceAddress
    								);
    										
		$soapData = $this->__encsoap($soapDataArray);
		$soapResponse = $this->secpki->__soapCall(
    						// "seccommerce.secappserver.pki.PKIUserSecAppServer.requestAuthSession",
    						"requestAuthSession", 
    						array(new SoapParam($soapData, "seccommerce.mobile.bo.ReqRequestAuthSession")));

		return $soapResponse;
    }
    
    /**
     * Checks the state of an authentication session
     */
     function getAuthSessionState($authSessionId){
    	$soapDataArray = array("authSessionID" => $authSessionId);
    										
		$soapData = $this->__encsoap($soapDataArray);
		$soapResponse = $this->secpki->__soapCall(
    						// "seccommerce.secappserver.pki.PKIUserSecAppServer.getAuthSessionState",
    						"getAuthSessionState", 
    						array(new SoapParam($soapData, "seccommerce.mobile.bo.ReqGetAuthSessionState")));

		return $soapResponse;
    }
    
    /**
     * Cancel the authentication session
     */
    function cancelAuthSession($authSessionId){
    	$soapDataArray = array("authSessionID" => $authSessionId);
    										
		$soapData = $this->__encsoap($soapDataArray);
		$soapResponse = $this->secpki->__soapCall(
    						// "seccommerce.secappserver.pki.PKIUserSecAppServer.cancelAuthSession",
    						"cancelAuthSession", 
    						array(new SoapParam($soapData, "seccommerce.mobile.bo.ReqCancelAuthSession")));

		return $soapResponse;
    }
}

?>
