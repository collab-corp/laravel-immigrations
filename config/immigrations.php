<?php return [
	/*
	|--------------------------------------------------------------------------
	| Immigrations root path
	|--------------------------------------------------------------------------
	|
	| Here you may specify the filesystem path to where your Immigrations reside.
	|
	*/
	'root' => database_path('Immigrations'),

	/*
	|--------------------------------------------------------------------------
	| Immigrations database connections
	|--------------------------------------------------------------------------
	|
	| Here you may specify the database connection to immigrate from.
	|
	*/
	'immigrate_from' => env('IMMIGRATE_FROM', 'old_database'),

	/*
	|--------------------------------------------------------------------------
	| Immigration progress bar
	|--------------------------------------------------------------------------
	|
	| Here you may specify the configurations for the progress bar,
	| these include the bar and progress characters as well as the format.
	|
	*/
	'progress' => [
		'bar_character' => "💣",
		'progress_character' => "🔥",
		'format' => 'debug'
	]
];