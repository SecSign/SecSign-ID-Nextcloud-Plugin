<?php

declare(strict_types = 1);

/**
 * Copyright SecSign 2018
 */

namespace OCA\SecSignID\Service;

use OCP\IUser;

interface IAPI {

	const STATE_DISABLED = 0;
	const STATE_CREATED = 1;
	const STATE_ENABLED = 2;

	/**
	 * Checks if given User has created a SecSign ID
	 * @param IUser $user
	 * @return bool if the user has a secsign id registered
	 */
	public function hasSecSignID(IUser $user): bool;

	/**
	 * @param string some secsignid
	 */
	public function requestAuthSession($secsignid);

	/**
	 * @return bool
	 */
	public function isSessionAccepted(): bool;

	/**
	 * @return bool
	 */
	public function isSessionPending(): bool;

}
