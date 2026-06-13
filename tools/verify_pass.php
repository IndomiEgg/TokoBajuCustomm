<?php
$pass = $argv[1] ?? 'admin1234';
$hash = $argv[2] ?? '$2y$10$fh.c22j058qChlR.cm3Yx.UykXqDqVji7O8EXh5ZB7OoNDf6MSiHS';
var_export(password_verify($pass, $hash));
echo PHP_EOL;