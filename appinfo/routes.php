<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\SecSignID\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
$application = new Application();

$routes = [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'settings#test', 'url' => '/', 'verb' => 'POST'],
	   ['name' => 'login#authstate', 'url' => '/login/state', 'verb' => 'POST'],
	   ['name' => 'id#create', 'url' => '/id/create/{id}', 'verb' => 'POST'],
	   ['name' => 'id#index', 'url' => '/id/', 'verb' => 'GET'],
	   ['name' => 'secsign#state', 'url' => '/2fa_state', 'verb' => 'GET']
];

$application->registerRoutes($this,['routes' => $routes]);
