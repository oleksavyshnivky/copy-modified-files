Файли проекту:
- app/config.php
- app/lastdate.txt
- app/tasks/modified.php
- app/log/modified.log

// ————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————

config.php 

— повинен містити такі змінні:

$_OLD — директорія, з якої потрібно копіювати файли
$_NEW — директорія, в яку потрібно копіювати файли
$exclude — масив імен файлів і директорій, які потрібно пропускати
$config_scp — масив налаштувань для SCP-підключення:
- server 
- username
- pass
- scp_target_dir — шлях до директорії, в яку потрібно копіювати файли
- scp_target_dir_short — той самий шлях, але без volume1, якщо це Synology

// ————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————

app/lastdate.txt 

— містить дату останнього минулого виконання сценарію. У випадку ручної правки потрібно вписати таку дату, 
яка обробиться функцією strtotime

// ————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————

app/tasks/modified.php

— копіювання з $_OLD у $_NEW файлів, змінених після дати з lastdate.txt

// ————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————
