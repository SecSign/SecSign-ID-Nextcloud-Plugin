<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'], 
		/* Secsign API */
	   ['name' => 'secsign#state', 'url' => '/state/', 'verb' => 'GET'],
	   ['name' => 'secsign#sessionState', 'url' => '/state/', 'verb' => 'POST'],
	   ['name' => 'secsign#cancel', 'url' => '/cancel/', 'verb' => 'POST'],
	   ['name' => 'secsign#cancelSession', 'url' => '/cancelSession/', 'verb' => 'POST'],
	   ['name' => 'secsign#idExists', 'url' => '/exists/', 'verb' => 'GET'],
	   ['name' => 'secsign#givenIdExists', 'url' => '/exists/', 'verb' => 'POST'],
	   ['name' => 'secsign#getID', 'url' => '/id/', 'verb' => 'GET'],
	   /* User Config */
	   ['name' => 'user#setID', 'url' => '/id/enable/', 'verb' => 'POST'],
	   ['name' => 'user#disableID', 'url' => '/id/disable/', 'verb' => 'POST'],
	   ['name' => 'user#getUsers', 'url' => '/ids/', 'verb' => 'GET'],
	   ['name' => 'user#findCurrent', 'url' => '/ids/current/', 'verb' => 'GET'],
	   ['name' => 'user#usersWithIds', 'url' => '/ids/users/', 'verb' => 'GET'],
	   ['name' => 'user#saveChanges', 'url' => '/ids/update/', 'verb' => 'POST'],
	   /* User Permissions */
	   ['name' => 'config#allowUserEdit', 'url' => '/allowEdit/', 'verb' => 'POST'],
	   ['name' => 'config#getAllowUserEdit', 'url' => '/allowEdit/', 'verb' => 'GET'],
	   ['name' => 'config#canUserEdit', 'url' => '/canEdit/', 'verb' => 'GET'],
	   /* Onboarding */
	   ['name' => 'config#getOnboarding', 'url' => '/onboarding/', 'verb' => 'GET'],
	   ['name' => 'config#changeOnboarding', 'url' => '/onboarding/', 'verb' => 'POST'],
	   ['name' => 'config#getIdChoiceAllowed', 'url' => '/onboarding/choice/', 'verb' => 'GET'],
	   ['name' => 'config#getIdChoiceAllowedForUsers', 'url' => '/onboarding/choice/all/', 'verb' => 'GET'],
	   ['name' => 'config#setIdChoiceAllowed', 'url' => '/onboading/choice/', 'verb' => 'POST'],
	   /* Server Config */
	   ['name' => 'config#saveServer', 'url' => '/server/', 'verb' => 'POST'],
	   ['name' => 'config#saveServerMobile', 'url' => '/server/mobile/', 'verb' => 'POST'],
	   ['name' => 'config#getServer', 'url' => '/server/', 'verb' => 'GET'],
	   ['name' => 'config#getServerMobile', 'url' => '/server/mobile/', 'verb' => 'GET'],
	   ['name' => 'config#getQR', 'url' => '/qr/', 'verb' => 'GET'],
	   ['name' => 'config#getQRForId', 'url' => '/qr/{secsignid}/', 'verb' => 'GET'],
    ]
];
