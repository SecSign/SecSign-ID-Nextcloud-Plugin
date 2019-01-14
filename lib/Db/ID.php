<?php

declare(strict_types = 1);

namespace OCA\SecSignID\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getSecsignid()
 * @method void setSecsignid(string $secsignid)
 * @method int getEnabled()
 * @method void setEnabled(int $state)
 */
class ID extends Entity {

	/** @var string */
	protected $userId;

	/** @var string */
	protected $secsignid;

	/** @var int */
	protected $enabled;

	public function __construct(){
		$this->addType('enabled','integer');
	}

	public function jsonSerialize(){
		return [
			'userId' => $this->userId,
			'secsignid' => $this->secsignid,
			'enabled' => $this->enabled,
			'id' => $this->id
		];
	}
}