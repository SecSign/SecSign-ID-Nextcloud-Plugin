<?php
/**
 * List of all necessary routes.
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'page#do_echo', 'url' => '/echo', 'verb' => 'POST'],
	   ['name' => 'secsign#state', 'url' => '/state/', 'verb' => 'GET'],
	   ['name' => 'secsign#cancel', 'url' => '/cancel/', 'verb' => 'POST'],
	   ['name' => 'secsign#idExists', 'url' => '/exists/', 'verb' => 'GET'],
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
	   ['name' => 'config#getOnboarding', 'url' => '/onboarding/', 'verb' => 'GET'],
	   ['name' => 'config#changeOnboarding', 'url' => '/onboarding/', 'verb' => 'POST'],
	   /* Server addresses */
	   ['name' => 'config#saveServer', 'url' => '/server/', 'verb' => 'POST'],
	   ['name' => 'config#saveServerMobile', 'url' => '/server/mobile/', 'verb' => 'POST'],
	   ['name' => 'config#getServer', 'url' => '/server/', 'verb' => 'GET'],
	   ['name' => 'config#getServerMobile', 'url' => '/server/mobile/', 'verb' => 'GET'],
	   ['name' => 'config#getQR', 'url' => '/qr/', 'verb' => 'GET'],
    ]
];
