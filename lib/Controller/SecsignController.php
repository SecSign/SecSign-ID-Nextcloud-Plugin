<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OC\Authentication\TwoFactorAuth\MandatoryTwoFactor;

use OCP\AppFramework\Controller;
use OCA\SecSignID\Service\IAPI;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;
use OCA\SecSignID\Provider\SecSign2FA;
use OCA\SecSignID\Service\PermissionService;

/**
 * The SecSignController links to required templates and handles requests to the server.
 */
class SecsignController extends Controller {
	private $userId;

	/** @var IAPI */
	private $iapi;

	private $mapper;

	private $manager;

	private $registry;

	private $provider;

	private $permissions;

	private $enforce;

	private $groupmanager;

	public function __construct($AppName, IRequest $request,		
								$UserId, IAPI $iapi,
								IDMapper $mapper, IUserManager $manager, IRegistry $registry,
								SecSign2FA $provider, PermissionService $permission, IGroupManager $groupmanager, MandatoryTwoFactor $enforce){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iapi = $iapi;
		$this->mapper = $mapper;
		$this->manager = $manager;
		$this->registry = $registry;
		$this->provider = $provider;
		$this->permissions = $permission;
		$this->groupmanager = $groupmanager;
		$this->enforce = $enforce;
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function state(){
		$accepted = $this->iapi->isSessionAccepted();
		return array('accepted' => $accepted);
	}

	/**
	 * Sets a SecSign ID for the current user.
	 * 
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * 
	 * @param string $secsignid
	 */
	public function setID(string $secsignid){
		$entity = new ID();
		$entity->setUserId($this->userId);
		$entity->setSecsignid($secsignid);
		$entity->setEnabled(1);
		$this->changeUserState(true, $this->userId);
		return $this->mapper->addUser($entity)->jsonSerialize();
	}

	/**
	 * Disables the current users 2FA.
	 * 
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function disableID(){
		$this->changeUserState(false, $this->userId);
		return $this->mapper->disableUser($this->userId)->jsonSerialize();
	}

	/**
	 * Cancels the pending authsession-.
	 * 
	 * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
	 */
	public function cancel(){
		$this->iapi->cancelAuthSession();
	}

	/**
	 * Gets all users joined with their corresponding SecSign IDs.
	 * 
	 * @NoCSRFRequired
	 */
	public function usersWithIds(){
		$data = $this->mapper->getUsersAndIds();
		foreach($data as &$user){
			$user['enabled'] = $this->getUserState($user['uid']) ? "1" : "0";
			$user['enforced'] = $this->enforcedForUser($user['uid']) ? "1":"0";
		}
		return $data;
	}

	/**
	 * Checks if 2FA is enforced for a given user
	 * 
	 * @param string $uid of user
	 * @return bool
	 */
	private function enforcedForUser($uid): bool {
		$user = $this->manager->get($uid);
		return $this->enforce->isEnforcedFor($user);
	}

	/**
	 * Saves all changes made in the user management screen.
	 * 
	 * @NoCSRFRequired
	 * 
	 * @param array $data
	 */
	public function saveChanges($data){
		foreach($data as &$user){
			$id = new ID();
			$id->setUserId($user[uid]);
			$id->setSecsignid($user[secsignid]);
			$id->setEnabled($user[enabled]);
			$this->changeUserState($user[enabled], $user[uid]);
			$this->mapper->addUser($id);
		}
		return $this->usersWithIds();
	}

	/**
	 * Gets all users.
	 * 
	 * @NoCSRFRequired
	 */
	public function getUsers(){
		$ids = $this->mapper->findAll();
		foreach ($ids as &$id){
			$id = $id->jsonSerialize();
		}
		return $ids;
	}

	/**
	 * Finds the SecSign ID for the current user.
	 * 
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function findCurrent(){
		$current = $this->mapper->find($this->userId);
		if($current !== null){
			return $current->jsonSerialize();
		}else{
			return null;
		}
	}

	/**
	 * Allows users to edit the settings of their SecSign 2FA
	 * 
	 * @NoCSRFRequired
	 * 
	 * @param boolean $allow
	 */
	public function allowUserEdit($allow){
		$this->permissions->setAppValue("allowEdit", $allow);
	}


	/**
	 * Gets status of editing permissions. Always returns true if user is an
	 * admin.
	 * 
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getAllowUserEdit(){
		$user = $this->manager->get($this->userId);
		$groups = $this->groupmanager->getUserGroups($user);
		foreach ($groups as &$group){
			if($group->getGID() === "admin"){
				return true;
			}
		}
		$allow =  $this->permissions->getAppValue("allowEdit");
		return allow == "" ? true : $allow == 1;
	}

	/**
	 * Enables or disables 2FA for the user with a given uid.
	 * 
	 * @param boolean $enable
	 * @param string $uid
	 */
	private function changeUserState($enable, $uid){
		$user = $this->manager->get($uid);
		if($enable){
			$this->registry->enableProviderFor($this->provider,$user);
		}else{
			$this->registry->disableProviderFor($this->provider,$user);
		}
	}

	/**
	 * Gets the state of 2FA for the user with a given uid.
	 * 
	 * @param string $uid
	 */
	private function getUserState($uid): bool {
		$user = $this->manager->get($uid);
		$states = $this->registry->getProviderStates($user);
		if(array_key_exists('secsignid',$states)){
			return $states['secsignid'];
		}else{
			return False;
		}
		
	}

}
