<?php
/**
 * @author Christoph Wurst <christoph@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\SecSignID\Provider;

use OCA\SecSignID\Service\IAPI;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\IUser;
use OCP\Template;
use OCA\SecSignID\Service\AuthSession;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;

class SecSign2FA implements IProvider {

	/** @var IAPI */
	private $iapi;
	

	private $mapper;

	private $userId;

	private $id;


	public function __construct(IAPI $iapi, $UserId, IDMapper $mapper){
		$this->iapi = $iapi;
		$this->userId = $UserId;
		$this->mapper = $mapper;
		$this->id = $this->mapper->find($this->userId);
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
	public function verifyChallenge(IUser $user,?string $challenge): bool {
		if ($challenge != null && $this->iapi->isSessionAccepted()) {
			return true;
		}
		return false;
	}
	/**
	 * Decides whether 2FA is enabled for the given user
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		$id = $this->mapper->find($this->userId);
		return $id != null && $id->getEnabled() == 1;
	}
}