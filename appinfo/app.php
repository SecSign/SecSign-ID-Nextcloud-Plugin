<?php

$app = new \OCA\SecSignID\AppInfo\Application();

$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener(
	'OC\Settings\Users::loadAdditionalScripts',
	function() {
		$authorized = json_decode(\OC::$server->getConfig()->getAppValue('secsignid', 'authorized', '["admin"]'));

		$loadScript = true;
		if(!empty($authorized)) {
			$userGroups = \OC::$server->getGroupManager()->getUserGroupIds(\OC::$server->getUserSession()->getUser());
			if (!array_intersect($userGroups, $authorized)) {
				$loadScript = false;
			}
		}
		if($loadScript){
			\OCP\Util::addScript('secsignid', 'users');
		}
	}
);