<?php

declare(strict_types = 1);

namespace OCA\SecSignID\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getSecSignID()
 * @method void setSecSignID(string $secsignid)
 * @method bool getEnabled()
 * @method void setEnabled(bool $state)
 */
class ID extends Entity {

	/** @var string */
	protected $userId;

	/** @var string */
	protected $secsignid;

	/** @var bool */
	protected $enabled;

	public function jsonSerialize(){
		return [
			'userId' => $this->userId,
			'secsignid' => $this->secsignid,
			'state' => $this->state
		];
	}

}