<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Provider;

use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCA\SecSignID\Service\IAPI;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\IUser;
use OCP\Template;
use OCP\Authentication\TwoFactorAuth\IRegistry;

use OCA\SecSignID\Service\AuthSession;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;
use OCA\SecSignID\Service\PermissionService;

/**
 * SecSign2FA is starts an authentication session once a user has
 * entered a correct username password combination.
 */
class SecSign2FA implements IProvider, IDeactivatableByAdmin {

	/** @var IAPI */
	private $iapi;
	
	private $registry;

	private $mapper;

	private $userId;

	private $id;

	private $permission;

	private $onboarding;


	public function __construct(IAPI $iapi, $UserId, IDMapper $mapper, 
								IRegistry $registry, PermissionService $permission){
		$this->iapi = $iapi;
		$this->userId = $UserId;
		$this->mapper = $mapper;
		$this->id = $this->mapper->find($this->userId);
		$this->registry = $registry;
		$this->permission = $permission;
		$this->onboarding = $this->permission->getAppValue("onboarding_enabled", false);
	}
	
	public function getId(): string {
		return 'secsignid';
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 */
	public function getDisplayName(): string {
		return 'SecSign ID';
	}

	/**
	 * Get the description for selecting the 2FA provider
	 */
	public function getDescription(): string {
		return 'SecSign ID 2FA';
	}

	/**
	 * Get the template for rending the 2FA provider view
	 */
	public function getTemplate(IUser $user): Template {
		if(!empty($_SESSION['session']))
		{
			$this->iapi->requestAuthSession($this->id->getSecsignid());
		}else{
			$this->iapi->requestAuthSession($this->id->getSecsignid());
		}
		return new Template('secsignid', 'challenge');
	}
	/**
	 * Verify the given challenge
	 */
	public function verifyChallenge(IUser $user, $challenge): bool {
		if ($challenge !== null && $this->iapi->isSessionAccepted()) {
			return true;
		}
		return false;
	}
	/**
	 * Decides whether 2FA is enabled for the given user
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		return $this->onboarding || ($this->id !== null && $this->id->getEnabled() === 1);
	}

	/**
	 * Allows an admin to deactivate 2FA for a given user via occ command line tool
	 */
	public function disableFor(IUser $user){
		$this->mapper->disableUser($user->getUID());
		$this->registry->disableProviderFor($this, $user);
	}
}