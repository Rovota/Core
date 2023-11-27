<?php /** @noinspection SpellCheckingInspection */

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => 'Français',
			'english-us' => 'French',
			'local' => __('French'),
		]
	],

	'units' => [
		'storage' => [
			'short' => ['o', 'Ko', 'Mo', 'Go', 'To', 'Eo', 'Zo', 'Yo'],
		],
	],

]);