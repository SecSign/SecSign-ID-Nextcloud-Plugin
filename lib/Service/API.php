<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
declare(strict_types = 1);
namespace OCA\SecSignID\Service;

use OCP\IUser;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Service\SecSignIDApi;
use OCA\SecSignID\Service\AuthSession;
use OCA\SecSignID\Service\PermissionService;
use OCP\IURLGenerator;

class API implements IAPI {

	private $idmapper;

	private $secsignid;

	private $authsession;

	private $server;
	private $fallback;
	private $serverport;
	private $fallbackport;
	private $url;

	public function __construct(IDMapper $idmapper, IURLGenerator $url, PermissionService $permissions){
		$this->idmapper = $idmapper;
		$this->server = (string) $permissions->getAppValue("server", "https://httpapi.secsign.com");
		$this->fallback = (string) $permissions->getAppValue("fallback", "https://httpapi2.secsign.com");
		$this->serverport =  (int) $permissions->getAppValue("serverport", 443);
		$this->fallbackport = (int) $permissions->getAppValue("fallbackport", 443);
		$this->url = $url->getBaseUrl();
	}

	/**
	 * Checks if user has a SecSign ID
	 * 
	 * @param $user is the user to be checked
	 * @return true if a user has a SecSign ID and it is enabled, else false
	 */
	public function hasSecSignID(IUser $user): bool{
		$id = $this->idmapper->find($user);
		$this->secsignid = $id->getSecsignid();
		return $secsignid !== null && $id->getEnabled();
	}

	/**
	 * Requests an authentication session for a given SecSign ID
	 * 
	 * @param secsignid
	 */
	public function requestAuthSession(String $secsignid): AuthSession{
		$secsignidapi = new SecSignIDApi($this->server,
										 $this->serverport, 				$this->fallback, 
										 $this->fallbackport);
		try{
			$_SESSION['session'] = $secsignidapi->requestAuthSession($secsignid,'SecSign Nextcloud Plugin', $this->url);
			return $_SESSION['session'];
		}catch(\Exception $e){
			throw($e);
		}
	}

	/**
	 * Checks if an authentication session has been accepted.
	 * 
	 * @return boolean
	 */
	public function isSessionAccepted(): bool{
		try{
			$authsession = $_SESSION['session'];
			if($authsession === null){
				return false;
			}
			$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
			$authSessionState = $secsignidapi->getAuthSessionState($authsession);
			return $authSessionState == AuthSession::AUTHENTICATED;
		}catch(Exception $e){
			throw $e;
		}
	}

	/**
	 * Checks the state of a given AuthSession
	 * 
	 * @return string
	 */
	public function getAuthState($session): string{
		$authsession = new AuthSession();
		$authsession->createAuthSessionFromArray($session);
		$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
		return $secsignidapi->getAuthSessionState($authsession);
	}

	/**
	 * Checks if there is an existing pending authentication session.
	 * 
	 * @return boolean
	 */
	public function isSessionPending(): bool{
		try{
			$authsession = $_SESSION['session'];
			if($authsession === null){
				return false;
			}
			$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
			$authSessionState = $secsignidapi->getAuthSessionState($authsession);
			return $authSessionState === AuthSession::PENDING;
		}catch(Exception $e){
			throw $e;
		}
	}

	/**
	 * Checks if an ID exists on the server by requesting an auth session. If an error occurs,
	 * it returns false, else it returns the Authsession that was started.
	 * 
	 * @param secsignid
	 * 
	 * @return array
	 */
	public function idExists(String $secsignid): array{
		try{
			$session = $this->requestAuthSession($secsignid);
			return array("session" => $session->getAuthSessionAsArray(), "exists" => true);
		}catch(\Exception $e){
			if($e->getCode() === 500 && strpos($e->getMessage(),"exist") !== false){
				return array(null, "exists" => false);
			}else{
				throw($e);
			}
		}
		
	}

	/**
	 * Cancels an existing authentication session.
	 */
	public function cancelAuthSession(){
		try{
			$authsession = $_SESSION['session'];
			if($authsession === null){
				return;
			}
			$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
			$secsignidapi->cancelAuthSession($authsession);
		}catch(Exception $e){
			throw $e;
		}
	}

	/**
	 * Cancels given authentication session.
	 * 
	 * @param $session
	 */
	public function cancelSession($session){
		try{
			$authsession = new AuthSession();
			$authsession->createAuthSessionFromArray($session);
			if($authsession === null){
				return;
			}
			$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
			$secsignidapi->cancelAuthSession($authsession);
		}catch(Exception $e){
			throw $e;
		}
	}
}
