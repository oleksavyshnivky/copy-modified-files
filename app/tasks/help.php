<?php 

echo <<<DOC
Остання зміна: 2018-09-24 17:07

Очистка логу:
> php index.php clearlog

Останні 10 дат з файлу lastdate.txt:
> php index.php dates

Останні X дат з файлу lastdate.txt, де X — у проміжку від 1 до 10000:
> php index.php dates limit=X

Даточас створення/зміни найсвіжіших 10 файлів за останні 30 діб:
> php index.php getlastdate

Даточас створення/зміни найсвіжіших X файлів за останні Y діб, де X і Y — у проміжку від 1 до 10000:
> php index.php getlastdate limit=X days=Y

Вибірка останніх доданих/змінених файлів
> php index.php

Вибірка останніх доданих/змінених файлів без збереження дати виконання
> php index.php save=0

Вибірка файлів, доданих/змінених після заданої дати  
> php index.php lastdate=2018-09-23

Вибірка файлів, доданих/змінених після дати X із файлу lastdate.txt (остання дата — 0)
> php index.php daten=X

DOC;

return '';
