<?php

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => '한국어',
			'english-us' => 'Korean',
			'local' => __('Korean'),
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
				return number_format($input, $decimals);
			},
		],
	],

]);