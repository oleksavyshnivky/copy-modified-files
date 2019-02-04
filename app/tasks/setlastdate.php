<?php

// ПРИЗНАЧЕННЯ: Запис нової опорної дати

// Нова опорна дата
$new_lastdate_txt = checkArgument('lastdate');
$new_lastdate = strtotime($new_lastdate_txt) ?: date('c');

file_put_contents('app/lastdate.txt', PHP_EOL . $new_lastdate, FILE_APPEND | LOCK_EX);
return true;
