<?php

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => '中文',
			'english-us' => 'Chinese',
			'local' => __('Chinese'),
		]
	],

]);