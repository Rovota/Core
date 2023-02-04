<?php

return array_replace_recursive(require __DIR__.'/base.php', [

	'about' => [
		'name' => [
			'native' => 'हिंदी',
			'english-us' => 'Hindi',
			'local' => __('Hindi'),
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
			// TODO: Use this for currency instead.
			// 'local' => function(mixed $input, int $decimals = 2) {
			// 	$sign = $input < 0 ? '-' : '';
			// 	$input = number_format($input, $decimals, '.', '');
			// 	list($number, $decimal) = explode('.', $input);
			//
			// 	$number = abs($number);
			// 	for ($i = 3; $i < strlen($number); $i += 3) {
			// 		$number = substr_replace($number, ',', -$i, 0);
			// 	}
			//
			// 	return sprintf('%s%s%s', $sign, $number, $decimals > 0 ? '.'.$decimal : '');
			// },
		],
	],

]);