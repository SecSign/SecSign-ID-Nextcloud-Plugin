<?php

namespace OCA\SecSignID\Service;
/*
* PHP class to gather all information about a so called authentication session.
* It contains the access pass, a request id, a session id and the secsign id.
*
* @author SecSign Technologies Inc.
* @copyright 2014-2017
*/
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
        
        
        /* 
         * the secsign id the authentication session has been craeted for
         */
        private $secSignID      = NULL;
        
        /*
         * authentication session id
         */
        private $authSessionID   = NULL;
        
        /*
         * the name of the requesting service. this will be shown at the smartphone
         */
        private $requestingServiceName = NULL;
        
        /*
         * the address, a valid url, of the requesting service. this will be shown at the smartphone
         */
        private $requestingServiceAddress = NULL;
        
        /*
         * the request ID is similar to a server side session ID. 
         * it is generated after a authentication session has been created. all other request like dispose, withdraw or to get the auth session state
         * will be rejected if a request id is not specified.
         */
        private $requestID        = NULL;
        
        /*
         * icon data of the so called access pass. the image data needs to be displayed otherwise the user does not know which access apss he needs to choose in order to accept the authentication session.
         */
        private $authSessionIconData = NULL;
        
        
        /*
         * Getter for secsign id
         */
        function getSecSignID()
        {
            return $this->secSignID;
        }
        
        /*
         * Getter for auth session id
         */
        function getAuthSessionID()
        {
            return $this->authSessionID;
        }
        
        /*
         * Getter for auth session requesting service
         */
        function getRequestingServiceName()
        {
            return $this->requestingServiceName;
        }
        
        /*
         * Getter for auth session requesting service
         */
        function getRequestingServiceAddress()
        {
            return $this->requestingServiceAddress;
        }
        
        /*
         * Getter for request id
         */
        function getRequestID()
        {
            return $this->requestID;
        }
        
        /*
         * Getter for icon data which needs to be display
         */
        function getIconData()
        {
            return $this->authSessionIconData;
        }
        
        /*
         * method to get string representation of this authentication session object
         */
        function __toString()
        {
            return $this->authSessionID . " (" . $this->secSignID . ", " . $this->requestingServiceAddress . ", icondata=" . $this->authSessionIconData . ")";
        }
        
        /*
         * builds an url parameter string like key1=value1&key2=value2&foo=bar
         */
        function getAuthSessionAsArray()
        {
            return array('secsignid'     => $this->secSignID,
                         'authsessionid' => $this->authSessionID,
                         'servicename'   => $this->requestingServiceName,
                         'serviceaddress'=> $this->requestingServiceAddress,
                         'authsessionicondata'=> $this->authSessionIconData,
                         'requestid'     => $this->requestID);
        }
        
        
        /*
         * Creates/Fills the auth session obejct using the given array. The array must use secsignid, auth session id etc as keys.
         */
        function createAuthSessionFromArray($array, $ignoreOptionalParameter = false)
        {
            if(! isset($array)){
                throw new Exception("Parameter array is NULL.");
            }
            
            if(! is_array($array)){
                throw new Exception("Parameter array is not an array. (array=" . $array . ")");
            }

            // check mandatory parameter
            if(! isset($array['secsignid'])){
                throw new Exception("Parameter array does not contain a value 'secsignid'.");
            }
            if(! isset($array['authsessionid'])){
                throw new Exception("Parameter array does not contain a value 'authsessionid'.");
            }
            if(! isset($array['servicename']) && !$ignoreOptionalParameter){
                throw new Exception("Parameter array does not contain a value 'servicename'.");
            }
            if(! isset($array['serviceaddress']) && !$ignoreOptionalParameter){
                throw new Exception("Parameter array does not contain a value 'serviceaddress'.");
            }
            if(! isset($array['requestid'])){
                throw new Exception("Parameter array does not contain a value 'requestid'.");
            }
            
            $this->secSignID                = $array['secsignid'];
            $this->authSessionID            = $array['authsessionid'];
            $this->requestingServiceName    = $array['servicename'];
            $this->requestingServiceAddress = $array['serviceaddress'];
            $this->requestID                = $array['requestid'];
            
            // everything else must exist
        	if(isset($array['authsessionicondata'])){
        		$this->authSessionIconData = $array['authsessionicondata'];
        	}
        }
}