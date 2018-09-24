<?php

// Дати зміни останніх 10 ($limit) файлів (якщо вони були не більше місяця тому)
$toplimit = checkArgument('limit');
$toplimit = filter_var($toplimit, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>10]]);

$daylimit = checkArgument('days');
$daylimit = filter_var($daylimit, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>30]]);

// Обмеження давності 
$timelimit = time() - 86400 * $daylimit;

//  Масив часів
$times = [];

function cmp($a, $b) {
	if ($a['time'] == $b['time']) return 0;
	return ($a['time'] < $b['time']) ? 1 : -1;
}

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

return true;
