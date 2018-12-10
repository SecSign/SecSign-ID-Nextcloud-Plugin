<?php

namespace OCA\SecSignID\Service;

use OCP\IUser;
use OCA\SecSignID\Provider\TwoFactorTestProvider;
use OCA\SecSignID\Service\AuthSession;

class SessionStateListener{

	private $iUser;
	private $ttp;
	private $session;
	private $secsignapi;

	public function __construct(IUser $iUser, TwoFactorTestProvider $ttp, AuthSession $session){
		$this->iUser = $iUser;
		$this->ttp = $ttp;
		$this->session = $_SESSION['session'];
		$this->secsignapi = new SecSignIDApi('https://httpapi.secsign.com',443);
	}

	public function checkState(){
		if($this->session==null){
			$this->session = $_SESSION['session'];
			sleep(3);
			checkState();
		}else{
			try{
				$authSessionState = $this->secsignapi->getAuthSessionState($this->session);
				if($authSessionState == AuthSession::AUTHENTICATED){
					$this->ttp->verifyChallenge($this->iUser,'testtest');
				}else{
					sleep(4);
					$this->checkState();
				}
			}catch(Exception $e){
				sleep(4);
				checkState();
			}
		}
	}
}