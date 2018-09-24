<?php

// Останні 10 ($limit) дат з файлу lastdate.txt

$limit = checkArgument('limit');
$limit = filter_var($limit, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>1000,'default'=>10]]);

$dates = array_reverse(explode(PHP_EOL, trim(file_get_contents('app/lastdate.txt'))));
$dates = array_slice($dates, 0, $limit);

foreach ($dates as $key => $value) echo $key, "\t", $value, PHP_EOL;

return '';
