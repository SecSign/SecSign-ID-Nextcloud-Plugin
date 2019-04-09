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

class API implements IAPI {

	private $idmapper;

	private $secsignid;

	private $authsession;

	private $server;
	private $fallback;
	private $serverport;
	private $fallbackport;

	public function __construct(IDMapper $idmapper, PermissionService $permissions){
		$this->idmapper = $idmapper;
		$this->server = $permissions->getAppValue("server");
		$this->fallback = $permissions->getAppValue("fallback");
		$this->serverport = $permissions->getAppValue("serverport");
		$this->fallbackport = $permissions->getAppValue("fallbackport");
		if($this->server === ""){
			$this->server     = (string) "https://httpapi.secsign.com";
            $this->fallback = (int) 443;
            $this->serverport = (string) "https://httpapi2.secsign.com";
			$this->fallbackport = (int) 443;
			$permissions->setAppValue("server",$this->server);
			$permissions->setAppValue("fallback",$this->fallback);
			$permissions->setAppValue("serverport",$this->serverport);
			$permissions->setAppValue("fallbackport",$this->fallbackport);
		}
	}

	/**
	 * Checks if user has a SecSign ID
	 * 
	 * @param $user is the user to be checked
	 * @return true if a user has a SecSign ID and it is enabled, else false
	 */
	public function hasSecSignID(IUser $user): bool{
		$id = $this->idmapper->find($user);
		$this->secsignid = $id->getSecSignID();
		return $secsignid !== null && $id->getEnabled();
	}

	/**
	 * Requests an authentication session for a given SecSign ID
	 * 
	 * @param secsignid
	 */
	public function requestAuthSession(String $secsignid){
		$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
		$_SESSION['session'] = $secsignidapi->requestAuthSession($secsignid,'SecSign Nextcloud Plugin', $this->server);
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
	 * Cancels an existion authentication session.
	 */
	public function cancelAuthSession(){
		try{
			$authsession = $_SESSION['session'];
			if($authsession === null){
				return;
			}
			$secsignidapi = new SecSignIDApi($this->server,
											 $this->serverport, 				$this->fallback, $this->fallbackport);
			$authSessionState = $secsignidapi->cancelAuthSession($authsession);
		}catch(Exception $e){
			throw $e;
		}
	}
}
