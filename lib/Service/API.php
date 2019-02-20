<?php

declare(strict_types = 1);

/**
 * Copyright SecSign 2018
 */

namespace OCA\SecSignID\Service;

use OCP\IUser;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Service\SecSignIDApi;
use OCA\SecSignID\Service\AuthSession;

class API implements IAPI {

	private $idmapper;

	private $secsignid;

	private $authsession;

	public function __construct(IDMapper $idmapper){
		$this->idmapper = $idmapper;
	}

	public function hasSecSignID(IUser $user): bool{
		$id = $this->idmapper->find($user);
		$this->secsignid = $id->getSecSignID();
		return $secsignid !== null && $id->getEnabled();
	}

	/*public function requestAuthSession(){
		$secsignidapi = new SecSignIDApi();
		$authsession = $secsignidapi->requestAuthSession($_POST[$secsignid],'SecSign Nextcloud Plugin',$_Server['https://httpapi.secsign.com']);
	}*/

	public function requestAuthSession($secsignid){
		$secsignidapi = new SecSignIDApi();
		$_SESSION['session'] = $secsignidapi->requestAuthSession($secsignid,'SecSign Nextcloud Plugin','https://httpapi.secsign.com');
	}

	public function isSessionAccepted(): bool{
		try{
			$authsession = $_SESSION['session'];
			if($authsession === null){
				return false;
			}
			$secsignidapi = new SecSignIDApi('https://httpapi.secsign.com',443);
			$authSessionState = $secsignidapi->getAuthSessionState($authsession);
			return $authSessionState === AuthSession::AUTHENTICATED;
		}catch(Exception $e){
			throw $e;
		}
	}

	public function isSessionPending(): bool{
		try{
			$authsession = $_SESSION['session'];
			if($authsession === null){
				return false;
			}
			$secsignidapi = new SecSignIDApi('https://httpapi.secsign.com',443);
			$authSessionState = $secsignidapi->getAuthSessionState($authsession);
			return $authSessionState === AuthSession::PENDING;
		}catch(Exception $e){
			throw $e;
		}
	}
}
