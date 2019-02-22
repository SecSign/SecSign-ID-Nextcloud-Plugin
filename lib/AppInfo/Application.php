<?php
/**
 * This class is required to prepare the use of SecSignController and API classes.
 * 
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
declare(strict_types=1);

namespace OCA\SecSignID\AppInfo;

use OCA\SecSignID\Service\IAPI;
use OCA\SecSignID\Service\API;
use OCA\SecSignID\Controller\SecsignController;
use OCP\AppFramework\App;

class Application extends App {

	public function __construct(array $urlParams = []) {
		parent::__construct('secsignid', $urlParams);

		$container = $this->getContainer();
		$container->registerAlias('SecsignController', SecsignController::class);
		$container->registerAlias(IAPI::class, API::class);
	}
}