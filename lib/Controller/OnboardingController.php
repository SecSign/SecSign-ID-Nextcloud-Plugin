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
use OCA\SecSignID\Service\SecsignService;

/**
 * The SecSignController links to required templates and handles requests to the server.
 */
class OnboardingController extends Controller {
	private $userId;

	private $mapper;

	private $manager;

    private $registry;
    
    private $secsignService;

	public function __construct($AppName, IRequest $request,		
								$UserId, 
								IDMapper $mapper, IUserManager $manager, IRegistry $registry, SecsignService $secsignService){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->mapper = $mapper;
		$this->manager = $manager;
		$this->registry = $registry;
		$this->secsignService = $secsignService;
	}

	/**
	 * Checks if the user already has a secsign id assigned
	 * 
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function hasID(): bool{
		$current = $this->mapper->find($this->userId);
		return isset($current) && $current->getSecsignid() != null;		
	}


	/**
	 * Sets a SecSign ID for the current user following successful onboarding.
	 * 
	 * @NoAdminRequired
	 * @PublicPage
	 * 
	 */
	public function setOnboardingID($provider){
		$entity = new ID();
		$entity->setUserId($this->userId);
		$secsignid = $this->secsignService->getID();
		$entity->setSecsignid($secsignid);
		$entity->setEnabled(1);
		$this->changeUserState(true, $this->userId, $provider);
		return $this->mapper->addUser($entity)->jsonSerialize();
	}

	/**
	 * Enables or disables 2FA for the user with a given uid.
	 * 
	 * @param boolean $enable
	 * @param string $uid
	 */
	private function changeUserState($enable, $uid, $provider){
		$user = $this->manager->get($uid);
		if($enable){
			$this->registry->enableProviderFor($provider, $user);
		}else{
			$this->registry->disableProviderFor($provider, $user);
		}
	}
}
