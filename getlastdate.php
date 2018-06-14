<?php
/**
 * Дата найсвіжішого файлу
 * 
 * @author ODE <dying.escape@gmail.com>
 * @copyright 2018
 */

header('Content-Type: text/plain; charset=utf-8');

// Дата найсвіжішого файлу
$mostfreshtime = 0;

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
	if (filemtime($pathname) > $mostfreshtime) {
		$mostfreshtime = filemtime($pathname);
	} elseif (filectime($pathname) > $mostfreshtime) {
		$mostfreshtime = filectime($pathname);
	}
}

echo date('c', $mostfreshtime), PHP_EOL;

