<?php


namespace OCA\SecSignID\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUid()
 * @method void setUid(string $uid)
 * @method string getDisplayname()
 * @method void setDisplayname(string $displayname)
 * @method string getPassword()
 * @method void setPassword(string $password)
 * @method string getUidLower()
 * @method void setUidLower(string $uidLower)
 * 
 * @author Björn Plüster
 * @copyright 2019 SecSign Technologies Inc.
 */
class User extends Entity {

	/** @var string */
	protected $uid;

	/** @var string */
	protected $displayname;

	/** @var string */
	protected $password;

	/** @var string */
	protected $uidLower;

	public function __construct(){
		
	}

	public function jsonSerialize(){
		return [
			'uid' => $this->uid,
			'displayname' => $this->displayname,
			'password' => $this->password,
			'uidLower' => $this->uidLower
		];
	}
}