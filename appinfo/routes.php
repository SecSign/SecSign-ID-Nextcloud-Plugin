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
	   ['name' => 'secsign#setID', 'url' => '/id/enable/', 'verb' => 'POST'],
	   ['name' => 'secsign#disableID', 'url' => '/id/disable/', 'verb' => 'POST'],
	   ['name' => 'secsign#getUsers', 'url' => '/ids/', 'verb' => 'GET'],
	   ['name' => 'secsign#findCurrent', 'url' => '/ids/current/', 'verb' => 'GET'],
	   ['name' => 'secsign#usersWithIds', 'url' => '/ids/users/', 'verb' => 'GET'],
	   ['name' => 'secsign#saveChanges', 'url' => '/ids/update/', 'verb' => 'POST'],
	   /* User Permissions */
	   ['name' => 'secsign#allowUserEdit', 'url' => '/allowEdit/{allow}/', 'verb' => 'POST'],
	   ['name' => 'secsign#getAllowUserEdit', 'url' => '/allowEdit/', 'verb' => 'GET']
    ]
];
