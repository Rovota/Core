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
		'units' => 'metric',
	],

	// 'date' => [
	// 	'formats' => [
	// 		'' => '',
	// 	],
	// 	'first_day_of_week' => 1
	// ],

	'units' => [
		'numbers' => [
			'short' => ['', 'K', 'M', 'B', 'T', 'Q'],
			'long' => ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion']
		],
		'storage' => [
			'short' => ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
			'long' => ['bytes', 'kilobytes', 'megabytes', 'gigabytes', 'terabytes', 'petabytes', 'exabytes', 'zettabytes', 'yottabytes']
		],
	],

];