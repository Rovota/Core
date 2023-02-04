<?php
/** @noinspection ALL */

/**
 * Inspired by briannesbitt/Carbon
 */

return [

	'about' => [
		'name' => [
			'native' => 'English',
			'english-us' => 'English',
			'local' => __('English'),
		],
		'direction' => 'ltr',
	],

	// 'date' => [
	// 	'formats' => [
	// 		'' => '',
	// 	],
	// 	'first_day_of_week' => 1
	// ],
	//
	// 'money' => [
	// 	'formats' => [
	// 		'' => '',
	// 	],
	// ],

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

];