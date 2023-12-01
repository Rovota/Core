<?php

return array_replace_recursive(require __DIR__ . '/base.php', [

	'about' => [
		'name' => [
			'native' => 'English (US)',
			'english-us' => 'English (US)',
			'local' => __('English (US)'),
		],
		'units' => 'imperial',
	],

]);