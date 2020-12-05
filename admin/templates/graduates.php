<?php
require "import/import.php";
$time_start = microtime(true);

function dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function csv_handler($filename, $offset, $length, $callback, $delimiter = ',')
{
    $position = 0;
    $handle = fopen($filename, "r");
    if ($handle === false) {
        return false;
    }
    fseek($handle, $offset);
    for ($i = 0; $i < $length; $i++) {
        $a = $callback(fgetcsv($handle, 300, $delimiter));
    }
    $position = ftell($handle);
    fclose($handle);
    return $position;
}


function _import_($length)
{
    $start = intval(file_get_contents(__DIR__ . '/step.txt'));
    $offset = get_n_csv(__DIR__ . '/import-exists.csv', $start, $length, 'import_certificate');
    file_put_contents(__DIR__ . '/step.txt', $offset);
}

//$handler = new ImportHandler(dirname(__DIR__) . '/import-exists.csv');
//$handler->import();
//$start = intval(file_get_contents(dirname(__DIR__) . '/step.txt'));

$time_end = microtime(true);
dump($time_end - $time_start);