<?php /** @noinspection SpellCheckingInspection */

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => 'Lietuvių',
			'english-us' => 'Lithuanian',
			'local' => __('Lithuanian'),
		]
	],

	'storage' => [
		'unit' => [
			'short' => ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'],
		],
	],

]);