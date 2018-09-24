<?php
/**
 * Копіювання змінених файлів
 * 
 * @author ODE <dying.escape@gmail.com>
 * @copyright 2018
 */

header('Content-Type: text/plain; charset=utf-8');
date_default_timezone_set('Europe/Kiev');

// Конфіг
define('DIR_BASE', basename(__DIR__));
include_once 'app/config.php';

// Вибір модуля
if (PHP_SAPI == 'cli') { 
	$task = $argc > 1 ? (strpos($argv[1], '=') === false ? $argv[1] : 'modified') : 'modified';
} else {
	$task = filter_input(INPUT_GET, 'task');
}
$task = sanitizeFileName($task);

$args = getArguments();

if (file_exists('app/tasks/'.$task.'.php')) {
	$res = include 'app/tasks/'.$task.'.php';
	if ($res === true) {
		echo 'Done.', PHP_EOL;
	} else {
		echo $res, PHP_EOL;
	}
} else {
	echo 'No such file.', PHP_EOL;
}

exit;

// ————————————————————————————————————————————————————————————————————————————————
// Рекурсивне видалення вмісту директорії
function removeDirectory($path) {
	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
	return;
}

function sanitizeFileName($file) {
	// Remove anything which isn't a word, whitespace, number
	// or any of the following caracters -_~,;:[]().
	$file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $file);
	// Remove any runs of periods (thanks falstro!)
	$file = preg_replace("([\.]{2,})", '', $file);

	return $file;
}

function getArguments() {
	$args = [];
	if (PHP_SAPI == 'cli') {
		global $argv;
		$tmp = $argv;
		array_shift($tmp);
		foreach ($tmp as $arg) {
			$e = explode('=', $arg);
			$args[$e[0]] = count($e) == 2 ? $e[1] : null;
		}
	} else {
		$args = $_GET; 
	}
	return $args;
}

function checkArgument($key) {
	global $args;
	return isset($args[$key]) ? $args[$key] : null;
}
