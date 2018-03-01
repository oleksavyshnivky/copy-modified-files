<?php
// Копіювання змінених/доданих після певної дати файлів у окрему директорію

// Нам потрібен чітко визначений часовий пояс, щоб не плутася при перегляді/ручному редагуванні файлу з датою
date_default_timezone_set('Europe/Kiev');

header('Content-Type: text/plain; charset=utf-8');

// Опорна дата (копіювати файли, змінені після неї)
$lastdate_txt = trim(file_get_contents('config-lastdate.txt'));
$lastdate = strtotime($lastdate_txt);

// Конфіг
include_once 'config.php';

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
	}
}
$log .= "\n";

// Запис у лог
file_put_contents('log/modified.log', $log, FILE_APPEND);

// Збереження останньої дати
file_put_contents('config-lastdate.txt', $new_lastdate);

exit;

// Рекурсивне видалення вмісту директорії
function removeDirectory($path) {
	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
	return;
}
