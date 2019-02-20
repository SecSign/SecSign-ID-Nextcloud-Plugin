<?php
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUserManager;

use OCP\AppFramework\Controller;
use OCA\SecSignID\Service\IAPI;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;
use OCA\SecSignID\Provider\SecSign2FA;
use OCP\Authentication\TwoFactorAuth\IRegistry;

class SecsignController extends Controller {
	private $userId;

	/** @var IAPI */
	private $iapi;

	private $mapper;

	private $manager;

	private $registry;

	private $provider;

	public function __construct($AppName, IRequest $request, $UserId, IAPI $iapi,
								IDMapper $mapper, IUserManager $manager, IRegistry $registry,
								SecSign2FA $provider){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iapi = $iapi;
		$this->mapper = $mapper;
		$this->manager = $manager;
		$this->registry = $registry;
		$this->provider = $provider;
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
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function disableID(){
		$this->changeUserState(false, $this->userId);
		return $this->mapper->disableUser($this->userId)->jsonSerialize();
	}

	/**
	 * @NoCSRFRequired
	 * 
	 */
	public function usersWithIds(){
		/*$ids = $this->userMapper->findAll();
		foreach ($ids as &$id){
			$id = $id->jsonSerialize();
		}
		return $ids;*/
		return $this->mapper->getUsersAndIds();
	}

	/**
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
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function findCurrent(){
		return $this->mapper->find($this->userId)->jsonSerialize();
	}
	

	private function changeUserState($enable, $uid){
		$user = $this->manager->get($uid);
		if($enable){
			$this->registry->enableProviderFor($this->provider,$user);
		}else{
			$this->registry->disableProviderFor($this->provider,$user);
		}
	}

}
