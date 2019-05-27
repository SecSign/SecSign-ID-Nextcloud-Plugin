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
	 * Checks the state of a given AuthSession
	 * 
	 * @return string
	 */
	public function getAuthState($session): string;

	/**
	 * Gets the SecSignID of a given session
	 * 
	 * @return string
	 */
	public function getSecSignID($session): string;

	/**
	 * Checks if there is an existing pending authentication session.
	 * 
	 * @return boolean
	 */
	public function isSessionPending(): bool;

	/**
	 * Checks if an ID exists on the server by requesting an auth session. If an error occurs,
	 * it returns false, else it returns the Authsession that was started.
	 * 
	 * @param secsignid
	 * 
	 * @return array
	 */
	public function idExists(String $secsignid): array;

	/**
	 * Cancels an existing authentication session.
	 */
	public function cancelAuthSession();

	/**
	 * Cancels given authentication session.
	 * 
	 * @param $session
	 */
	public function cancelSession($session);

}
