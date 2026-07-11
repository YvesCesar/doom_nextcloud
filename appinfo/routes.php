<?php

declare(strict_types=1);

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'api#getDoomBundle', 'url' => '/bundle/doom.jsdos', 'verb' => 'GET'],
		['name' => 'api#getEmulatorFile', 'url' => '/emulators/{filename}', 'verb' => 'GET'],
		['name' => 'api#getState', 'url' => '/jsdos-state', 'verb' => 'GET'],
		['name' => 'api#setKey', 'url' => '/jsdos-key', 'verb' => 'PUT'],
		['name' => 'api#deleteState', 'url' => '/jsdos-state', 'verb' => 'DELETE'],
	],
];
