<?php

declare(strict_types=1);

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'api#getDoomBundle', 'url' => '/bundle/doom', 'verb' => 'GET'],
	],
];
