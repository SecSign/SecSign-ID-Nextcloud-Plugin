<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Service;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUser;
use OCP\ISession;
use OCA\SecSignID\Service\AuthSession;

use OCA\SecSignID\Service\IAPI;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;
use OCA\SecSignID\Exceptions\SecsignException;

use OCA\SecSignID\Service\PermissionService;

/**
 * The SecsignService handles requests to the SecSign API.
 */
class SecSignService {
	private $userId;

	/** @var IAPI */
	private $iapi;

	private $mapper;

	private $permission;

	private $session;

	public function __construct($AppName, IRequest $request,		
								$UserId, IAPI $iapi,
								IDMapper $mapper, PermissionService $permission, ISession $session){
		$this->userId = $UserId;
		$this->iapi = $iapi;
		$this->mapper = $mapper;
		$this->permission = $permission;
		$this->session = $session;
	}

	/**
	 * Checks if the AuthSession saved in $_SESSION['session] has been accepted.
     * @return array with 'accepted' either true or false
     */
	public function state(){
		try{
			$accepted = $this->iapi->isSessionAccepted();
			return array('accepted' => $accepted);
		}catch(\Exception $e){
			throw new SecsignException($e->getMessage(), $e->getCode());
		}
		
	}

	/**
     * Checks the state of the given AuthSession.
     * 
     * @return the code according to the state. (See AuthSession)
	 * @param array $session 
     */
	public function sessionState($session){
		try{
			return $this->iapi->getAuthState($session);
		}catch(\Exception $e){
			throw new SecsignException($e->getMessage(), $e->getCode());
		}
		
	}

	/**
     * Checks if the ID assigned to this user exists on the Server.
     * 
     * @return boolean
     */
	public function idExists(){
		$secsignid = $this->getID();
		try{
			return $this->givenIdExists($secsignid);
		}catch(\Exception $e){
			throw new SecsignException($e->getMessage(), $e->getCode());
		}
		
	}

	/**
     * Checks if the given ID exists on the Server.
     * 
	 * @param string $secsignid
     * @return boolean
     */
	public function givenIdExists($secsignid){
		$values = $this->iapi->idExists($secsignid);
		if(!empty($values['session'])){
			$this->session['session'] = $values['session'];
		}
		return $values;
	}

	/**
	 * Cancels the pending authsession in Session Variable.
	 * 
	 */
	public function cancel(){
        try{
            $this->iapi->cancelAuthSession();
        }catch(\Exception $e){
            throw new SecsignException($e->getMessage(), $e->getCode());
        }
	}

	/**
	 * Cancels the pending authsession.
	 * 
	 * @param array $session
	 */
	public function cancelSession($session){
        try{
            $this->iapi->cancelSession($session);
        }catch(\Exception $e){
            throw new SecsignException($e->getMessage(), $e->getCode());
        }
		
	}

	/**
	 * Returns the secsignid for the current user
     * 
     * @return string
	 */
	public function getID(){
		$current = $this->mapper->find($this->userId);
		if(isset($current) && !empty($current->getSecsignid())){
			return $current->getSecsignid();
		}else{
			return $this->userId . "@" . $this->permission->getAppValue("onboarding_suffix","test");
		}		
	}
}
