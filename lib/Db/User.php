<?php


namespace OCA\SecSignID\Db;

use OCP\AppFramework\Db\Entity;

/**
 * method string getUid()
 * method void setUid(string $uid)
 * method string getDisplayname()
 * method void setDisplayname(string $uid)
 * method string getPassword()
 * method void setPassword(string $uid)
 * method string getUidLower()
 * method void setUidLower(string $uid)
 */
class User extends Entity {

	protected $uid;

	protected $displayname;

	protected $password;

	protected $uidLower;
}