<?php

// Файли, змінені після вказаної дати (чи дати останнього виконання цього завдання)

// Опорна дата (копіювати файли, змінені після неї)
$lastdate_txt = checkArgument('lastdate');
if (!$lastdate_txt) {
	$dateN = (int)checkArgument('daten');
	if ($dateN > 0) {
		$dates = explode(PHP_EOL, trim(file_get_contents('app/lastdate.txt')));
		if (isset($dates[$dateN])) $lastdate_txt = $dates[$dateN];
	}
	if (!$lastdate_txt) {
		$dates = explode(PHP_EOL, trim(file_get_contents('app/lastdate.txt')));
		$lastdate_txt = end($dates);
	}
}
$lastdate = strtotime($lastdate_txt);

// Нова опорна дата
$new_lastdate_unix = time();
$new_lastdate = date('c', $new_lastdate_unix);

// Директорія проекту
$path = realpath($_OLD);

// Очистка директорії результату
foreach (new DirectoryIterator($_NEW) as $fileInfo) {
	if (!$fileInfo->isDot()) {
		$pathname = $fileInfo->getPathname();
		is_dir($pathname) ? removeDirectory($pathname) : unlink($pathname);
	}
}

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

$log = "— {$new_lastdate} —\n";
$counter = 0;
foreach ($iterator as $pathname => $fileInfo) {
	if (filemtime($pathname) > $lastdate or filectime($pathname) > $lastdate) {
		$subpath = $iterator->getSubPath();
		$basename = basename($pathname);

		// Пропускати файли з іменем як у $exclude
		if (in_array($basename, $exclude)) continue;

		if (!file_exists("{$_NEW}/{$subpath}")) {
			mkdir("{$_NEW}/{$subpath}", 0777, true);
		}

		copy($pathname, "{$_NEW}/{$subpath}/{$basename}");

		echo "{$pathname}\n";
		$log .= "{$pathname}\n";
		$counter++;
	}
}
$log .= "\n";

// Запис у лог
file_put_contents('app/log/modified.log', $log, FILE_APPEND);

// Збереження останньої дати
if (checkArgument('save') !== '0' and $counter) file_put_contents('app/lastdate.txt', PHP_EOL . $new_lastdate, FILE_APPEND | LOCK_EX);
return true;

