<?php
/**
 * Дата найсвіжішого файлу
 * 
 * @author ODE <dying.escape@gmail.com>
 * @copyright 2018
 */

header('Content-Type: text/plain; charset=utf-8');

// Обмеження давності 
$timelimit = time() - 86400 * 30; 
// 
$toplimit = 10;

//  Масив часів
$times = [];

function cmp($a, $b) {
	if ($a['time'] == $b['time']) return 0;
	return ($a['time'] < $b['time']) ? 1 : -1;
}

// Конфіг
include_once 'config.php';

// Директорія проекту
$path = realpath($_OLD);

// Рекурсивний перебір директорій і файлів проекту
/**
 * @param SplFileInfo $file
 * @param mixed $key
 * @param RecursiveCallbackFilterIterator $iterator
 * @return bool True if you need to recurse or if the item is acceptable
 */
$filter = function ($file, $key, $iterator) use ($exclude) {
	if ($iterator->hasChildren() && !in_array($file->getFilename(), $exclude)) {
		return true;
	}
	return $file->isFile();
};

$innerIterator = new RecursiveDirectoryIterator(
	$path,
	RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator = new RecursiveIteratorIterator(
	new RecursiveCallbackFilterIterator($innerIterator, $filter)
);

foreach ($iterator as $pathname => $fileInfo) {
	if (filemtime($pathname) > $timelimit) $times[] = ['file' => $pathname, 'time' => filemtime($pathname)];
	elseif (filectime($pathname) > $timelimit) $times[] = ['file' => $pathname, 'time' => filectime($pathname)];
}

usort($times, 'cmp');
$times = array_slice($times, 0, $toplimit);

foreach ($times as $row) {
	echo date('c', $row['time']), ' — ', $row['file'], PHP_EOL;
}

