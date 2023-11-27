<?php

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => '日本人',
			'english-us' => 'Japanese',
			'local' => __('Japanese'),
		]
	],

]);