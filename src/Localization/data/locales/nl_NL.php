<?php

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => 'Nederlands',
			'english-us' => 'Dutch',
			'local' => __('Dutch'),
		]
	],

	'storage' => [
		'unit' => [
			'short' => ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'],
		],
	],

	'number' => [
		'format' => [
			'default' => function(mixed $input, int $decimals = 2) {
				return number_format($input, $decimals, ',', '.');
			},
		],
	],

]);