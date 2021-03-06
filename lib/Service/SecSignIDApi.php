<?php
    
//
// SecSign ID Api in php. This file is slightly modified for use in SecSign Nextcloud plugin.
// Source: https://github.com/SecSign/secsign-php-api
//
// (c) 2014-2019 SecSign Technologies Inc.
//
namespace OCA\SecSignID\Service;
use \Exception;    
define("SCRIPT_VERSION", '1.47');

/*
* PHP class to connect to a secsign id server. the class will check secsign id server certificate and request for authentication session generation for a given
* user id which is called secsign id. Each authentication session generation needs a new instance of this class.
*
* @author SecSign Technologies Inc.
* @copyright 2014-2019
*/
class SecSignIDApi
{
        // once created the api can be used to create a single request for a certain specified userid
        private $secSignIDServer     = NULL;
        private $secSignIDServerPort = NULL;
        private $secSignIDServer_fallback = NULL;
        private $secSignIDServerPort_fallback = NULL;

        // numeric script version.
        private $scriptVersion  = 0;
        private $referer        = NULL;
        private $logger = NULL;

        private $pluginName = NULL;
        private $showAccesspass = NULL;
        private $lastResponse = NULL;


        /*
         * Constructor
         */
        function __construct($server, $port, $fallback, $fallback_port)
        {
            // secsign id server: hostname and port
            $this->secSignIDServer     = (string) $server;
            $this->secSignIDServerPort = (int) $port;
            $this->secSignIDServer_fallback = (string) $fallback;
            $this->secSignIDServerPort_fallback = (int) $fallback_port;

            // script version from cvs revision string
            $this->scriptVersion = SCRIPT_VERSION;

            // use a constant string rather than using the __CLASS__ definition
            // because this could cause problems when the class is in a submodule
            $this->referer = "SecSignIDApi_PHP";
        }

        



        /*
         * Destructor
         */
        function __destruct()
        {
            $this->secSignIDServer = NULL;
            $this->secSignIDServerPort   = NULL;
            $this->secSignIDServer_fallback = NULL;
            $this->secSignIDServerPort_fallback   = NULL;
            $this->pluginName   = NULL;
            $this->showAccesspass = NULL;
            $this->scriptVersion = NULL;
            $this->logger = NULL;
        }

        /**
         * Function to check whether curl is available
		 */
        function prerequisite()
        {
            if(! function_exists("curl_init")){
                return false;
            }

            if(! function_exists("curl_exec")){
                return false;
            }

            if(! is_callable("curl_init", true, $callable_name)){
                return false;
            }

            if(! is_callable("curl_exec", true, $callable_name)){
                return false;
            }

            return true;
        }

        /*
         * Sets a function which is used as a logger
         */
        function setLogger($logger)
        {
            if($logger !== NULL && isset($logger) && is_callable($logger) === TRUE){
                $this->logger = $logger;
            }
        }

        /*
         * logs a message if logger instance is not NULL
         */
        private function log($message)
        {
            if($this->logger !== NULL){
                $logMessage = __CLASS__ . " (v" . $this->scriptVersion . "): " . $message;
                call_user_func($this->logger, $logMessage);
            }
        }

        /*
         * Sets an optional plugin name
         */
        function setPluginName($pluginName)
        {
            $this->pluginName = $pluginName;
        }

        /*
         * Sets an optional parameter to determine if the accesspass should be used
         */
        function setShowAccesspass($showAccesspass)
        {
            $this->showAccesspass = $showAccesspass;
        }

        /*
         * Gets last response
         */
        function getResponse()
        {
            return $this->lastResponse;
        }


        /*
         * Send query to secsign id server to create an authentication session for a certain secsign id. This method returns the authentication session itself.
         */
        function requestAuthSession($secsignid, $servicename, $serviceadress)
        {
            $this->log("Call of function 'requestAuthSession'.");

            if(empty($servicename)){
                $this->log("Parameter \$servicename must not be null.");
                throw new \Exception("Parameter \$servicename must not be null.");
            }

            if(empty($serviceadress)){
                $this->log("Parameter \$serviceadress must not be null.");
                throw new \Exception("Parameter \$serviceadress must not be null.");
            }

            if(empty($secsignid)){
                $this->log("Parameter \$secsignid must not be null.");
                throw new \Exception("Parameter \$secsignid must not be null.");
            }

			// secsign id is always key insensitive. comvert to lower case and trim whitespace
            $secsignid = trim(strtolower($secsignid));

            // check again. probably just spacess which will ne empty after trim()
            if(empty($secsignid)){
                $this->log("Parameter \$secsignid must not be null.");
                throw new \Exception("Parameter \$secsignid must not be null.");
            }

            $requestParameter = array('request' => 'ReqRequestAuthSession',
                                      'secsignid' => $secsignid,
                                      'servicename' => $servicename,
                                      'serviceaddress' => $serviceadress);

            if($this->pluginName !== NULL){
                $requestParameter['pluginname'] = $this->pluginName;
            }

            if($this->showAccesspass !== NULL){
                $requestParameter['showaccesspass'] = $this->showAccesspass;
            }

            $response = $this->send($requestParameter, NULL);

            $authSession = new AuthSession();
            $authSession->CreateAuthSessionFromArray($response);

            return $authSession;
        }


        /*
         * Gets the authentication session state for a certain secsign id whether the authentication session is still pending or it was accepted or denied.
         */
        function getAuthSessionState($authSession)
        {
            $this->log("Call of function 'getAuthSessionState'.");

            if($authSession === NULL || !($authSession instanceof AuthSession)){
                $message = "Parameter \$authSession is not an instance of AuthSession. get_class(\$authSession)=" . get_class($authSession);
                $this->log($message);
                throw new \Exception($message);
            }

            $requestParameter = array('request' => 'ReqGetAuthSessionState');
            $response = $this->send($requestParameter, $authSession);

            return $response['authsessionstate'];
        }


        /*
         * Cancel the given auth session.
         */
        function cancelAuthSession($authSession)
        {
            $this->log("Call of function 'cancelAuthSession'.");

            if($authSession === NULL || !($authSession instanceof AuthSession)){
                $message = "Parameter \$authSession is not an instance of AuthSession. get_class(\$authSession)=" . get_class($authSession);
                $this->log($message);
                throw new \Exception($message);
            }

            $requestParameter = array('request' => 'ReqCancelAuthSession');
            $response = $this->send($requestParameter, $authSession);

            return $response['authsessionstate'];
        }

        /*
         * build an array with all parameters which has to be send to server
         */
        private function buildParameterArray($parameter, $authSession)
        {
            //$mandatoryParams = array('apimethod' => $this->referer, 'scriptversion' => $this->scriptVersion);
            $mandatoryParams = array('apimethod' => $this->referer);

            if(isset($authSession))
            {
                // add auth session data to mandatory parameter array
                $authSessionData = array('secsignid' => strtolower($authSession->getSecSignID()),
                                         'authsessionid'  => $authSession->getAuthSessionID(),
                                         'requestid' => $authSession->getRequestID());

                $mandatoryParams = array_merge($mandatoryParams, $authSessionData);
            }
            return array_merge($mandatoryParams, $parameter);
        }


        /*
         * sends given parameters to secsign id server and wait given amount
         * of seconds till the connection is timed out
         */
        function send($parameter, $authSession)
        {
            $requestQuery = http_build_query($this->buildParameterArray($parameter, $authSession), '', '&');
            $timeout_in_seconds = 15;

            // create cURL resource
            $ch = $this->getCURLHandle($this->secSignIDServer, $this->secSignIDServerPort, $requestQuery, $timeout_in_seconds);
            $this->log("curl_init: " . $ch);

            // $output contains the output string
            $this->log("cURL curl_exec sent params: " . $requestQuery);
            $output = curl_exec($ch);
            if ($output === false)
            {
                $this->log("curl_error: " . curl_error($ch));
            }

            // close curl resource to free up system resources
            $this->log("curl_close: " . $ch);
            curl_close($ch);

            // check if output is NULL. in that case the secsign id might not have been reached.
            if($output === NULL)
            {
                $this->log("curl: output is NULL. Server " . $this->secSignIDServer . ":" . $this->secSignIDServerPort . " has not been reached.");

                if($this->secSignIDServer_fallback !== NULL)
                {
                    $this->log("curl: get new handle from fallback server.");
                    $ch = $this->getCURLHandle($this->secSignIDServer_fallback, $this->secSignIDServerPort_fallback, $requestQuery, $timeout_in_seconds);
                    $this->log("curl_init: " . $ch . " connecting to " . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));

                    // $output contains the output string
                    $output = curl_exec($ch);
                    if($output === NULL)
                    {
                        $this->log("output is NULL. Fallback server " . $this->secSignIDServer_fallback . ":" . $this->secSignIDServerPort_fallback . " has not been reached.");
                        $this->log("curl_error: " . curl_error($ch));
                        throw new \Exception("curl_exec error: can't connect to Server - " . curl_error($ch));
                    }

                    // close curl resource to free up system resources
                    $this->log("curl_close: " . $ch);
                    curl_close($ch);

                }
                else
                {
                    $this->log("curl: no fallback server has been specified.");
                }
            }
            $this->log("curl_exec response: " . ($output === NULL ? "NULL" : $output));
            $this->lastResponse = $output;

            return $this->checkResponse($output, TRUE); // will throw an \Exception in case of an error
        }


        /*
         * checks the secsign id server response string
         */
        private function checkResponse($response, $throwExcIfError)
        {
            if(! isset($response))
            {
                $this->log("Could not connect to host '" . $this->secSignIDServer . ":" . $this->secSignIDServerPort . "'");
                if($throwExcIfError)
                {
                    throw new \Exception("Could not connect to server.");
                }
            }

            $responseArray = array();

            // server send parameter strings like:
            // var1=value1&var2=value2&var3=value3&...
            $valuePairs = explode("&", $response);
            foreach($valuePairs as $pair)
            {
            	$exploded = explode("=", $pair, 2);
            	if (count($exploded) === 2)
            	{
                	list($key, $value) = $exploded;
                	$responseArray[$key] = $value;
                }
            }

            // check if server send a parameter named 'error'
            if(isset($responseArray['error']))
            {
                $this->log("SecSign ID server sent error. code=" . $responseArray['error'] . " message=" . $responseArray['errormsg']);
                if($throwExcIfError)
                {
                    throw new \Exception($responseArray['errormsg'], $responseArray['error']);
                }
            }
            return $responseArray;
        }


        /*
         * Gets a cURL resource handle.
         */
        private function getCURLHandle($server = NULL, $port = -1, $parameter, $timeout_in_seconds)
        {
            // create cURL resource
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $server);
            //curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_PORT, $port);
            //curl_setopt($ch, CURLOPT_SSLVERSION, 3);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0); // value 0 will strip header information in response

            // set connection timeout
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout_in_seconds);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

            // make sure the common name of the certificate's subject matches the server's host name
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            // validate the certificate chain of the server
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            //The CA certificates
            curl_setopt($ch, CURLOPT_CAINFO, realpath(dirname(__FILE__)) .'/curl-ca-bundle.crt');

            // add referer
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);

            // add all parameter and change request mode to POST
            curl_setopt($ch, CURLOPT_POST, 2);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);

            return $ch;
        }
}