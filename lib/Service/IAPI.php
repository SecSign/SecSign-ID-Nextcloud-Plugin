<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
declare(strict_types = 1);

namespace OCA\SecSignID\Service;

use OCP\IUser;

interface IAPI {

	const STATE_DISABLED = 0;
	const STATE_CREATED = 1;
	const STATE_ENABLED = 2;

	/**
	 * Checks if user has a SecSign ID
	 * 
	 * @param $user is the user to be checked
	 * @return true if a user has a SecSign ID and it is enabled, else false
	 */
	public function hasSecSignID(IUser $user): bool;

	/**
	 * Requests an authentication session for a given SecSign ID
	 * 
	 * @param secsignid
	 */
	public function requestAuthSession(String $secsignid);

	/**
	 * Checks if an authentication session has been accepted.
	 * 
	 * @return boolean
	 */
	public function isSessionAccepted(): bool;

	/**
	 * Checks if there is an existing pending authentication session.
	 * 
	 * @return boolean
	 */
	public function isSessionPending(): bool;

	/**
	 * Cancels an existion authentication session.
	 */
	public function cancelAuthSession();

}
