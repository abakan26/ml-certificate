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

function getDoubleEmail($filename)
{
    $count = 0;
    $handle = fopen($filename, "r");
    if ($handle === false) {
        print_r("Не удалось открыть файл $filename");
        return false;
    }
    while ($row = fgetcsv($handle, '300', ',')) {
        if (isDoubleEmail($row)) {
            writeResult($row);
            $count++;
        } else {
            writeResult($row, false);
        }
    }
    fclose($handle);
    echo '<br><br>';
    echo '******************************';
    print_r("Найдено $count записей");


}
function isDoubleEmail($row)
{
    if ( count(explode(',', trim($row[3]))) > 1 )  {
        return true;
    }
    if ( count(explode(';', trim($row[3]))) > 1 )  {
        return true;
    }
    if ( count(explode(' ', trim($row[3]))) > 1 )  {
        return true;
    }
    return false;
}
function writeResult($row, $condition = true)
{
    $filename = $condition ? 'double-email.csv' : 'other.csv';
    $fp = fopen(__DIR__ . "/$filename", 'a');
    fputcsv($fp, $row);
    fclose($fp);
    return true;
}
//$handler = new ImportHandler(__DIR__ . '/import-verify.csv', true);
//$handler->import();
//$start = intval(file_get_contents(dirname(__DIR__) . '/step.txt'));


$time_end = microtime(true);
dump($time_end - $time_start);
