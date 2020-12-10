<?php
$time_start = microtime(true);

/*
 * Выбрать сертификаты по certificate_template_id
 * Выбрать сертификаты по товару
 * Выбрать сертификаты по диапазону даты создания create_date
 * Выбрать сертификаты по диапазону даты выдачи date_issue
 */

/**
 * Удалить BOM из строки
 * @param string $str - исходная строка
 * @return string $str - строка без BOM
 */
function removeBOM($str = "")
{
    if (substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        return substr($str, 3);
    }
    return $str;
}

function dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function get_n_csv($filename, $offset, $length, $callback)
{

    $position = 0;
    $handle = fopen($filename, "r");

    if ($handle === false) {
        return false;
    }

    fseek($handle, $offset);

    for ($i = 0; $i < $length; $i++) {
        $callback(fgetcsv($handle, 300, ","));
    }
    $position = ftell($handle);
    fclose($handle);
    return $position;
}

function write_not_exist($fields)
{
    $fp = fopen(__DIR__ . '/no-exists.csv', 'a');
    fputcsv($fp, $fields);
    fclose($fp);
}

function write_fail($data, $note)
{
    $fields = array_merge($data, [$note]);
    $fp = fopen(__DIR__ . '/user-exists-fail.csv', 'a');
    fputcsv($fp, $fields);
    fclose($fp);
}

function is_coincidence($user_data, $table_data)
{
    foreach (array_keys($user_data) as $key) {
        if (!empty($user_data[$key]) && $user_data[$key] !== $table_data[$key]) {
            return false;
        }
    }
    return true;
}

function handler_existing_user($user, $data)
{
    $user_id = $user->ID;
    $user_data = [
        'first_name' => get_user_meta($user_id, 'first_name', true),
        'last_name' => get_user_meta($user_id, 'last_name', true),
        'surname' => get_user_meta($user_id, 'surname', true),
    ];
    $table_data = [
        'first_name' => trim($data[1]),
        'last_name' => trim($data[0]),
        'surname' => trim($data[2]),
    ];
    print_r('<br><br>');

    /*Проверка на пустоту данных из таблицы*/
    $empty_val = array_filter(array_values($table_data), function ($val) {
        return empty($val);
    });
    if (count($empty_val) === 3) {
        write_fail($data, 'В таблице пропущены ФИО');
        return;
    }

    $series = trim($data[4]);
    $number = trim($data[5]);
    try {
        $date = new DateTime(trim($data[6]));
    } catch (Exception $e) {
        echo $e->getMessage();
        return;
    }
    $date_issue = $date->format('Y-m-d');
    $create_date = $date_issue;

    /* Не указано ни имя, ни фамилия*/
    if (empty($user_data['first_name']) && empty($user_data['last_name'])) {
//        do_import($data);
        print_r($data);
        return;
    }
    return;
    if (is_coincidence($user_data, $table_data)) {
        do_import($data);
        return;
    }
    $no_coincidence = array_reduce(array_keys($user_data), function ($carry, $key) use ($user_data, $table_data) {
        return $carry . ($user_data[$key] !== ', ' . $table_data[$key] ? $key : '');
    });
    write_fail($data, 'Некоторые данные не совпадают: ' . $no_coincidence);
    return;
}

function do_import($fields)
{
    $fp = fopen(__DIR__ . '/import-exists.csv', 'a');
    fputcsv($fp, $fields);
    fclose($fp);
}

function import_handler($data)
{
    $email = trim($data[3]);
    print_r($email);
    if ($user = get_user_by('email', $email)) {
        handler_existing_user($user, $data);
        return;
    }
    return;
    write_not_exist($data);
}

function get_product_id_by_series($series)
{
    switch ($series) {
        case 'GV-01':
        case 'D-1':
            return 1717;
        case 'D-3':
            return 1695;
        case 'D-4':
            return 2240;
        case 'D-5':
            return 1072;
        case 'D-6':
            return 2234;
        case 'D-7':
            return 2238;
        case 'D-8':
            return 2236;
        case 'D-9':
            return 2618;
        case 'R-2':
            return 1058;
    }
}

function import_certificate($data)
{
    $email = trim($data[3]);
    if ($user = get_user_by('email', $email)) {
        $user_id = $user->ID;
        $first_name = get_user_meta($user_id, 'first_name', true);
        $last_name = get_user_meta($user_id, 'last_name', true);
        $surname = get_user_meta($user_id, 'surname', true);
        if (empty($first_name)) {
            update_user_meta($user_id, 'first_name', $data[1]);
        }
        if (empty($last_name)) {
            update_user_meta($user_id, 'last_name', $data[0]);
        }
        if (empty($surname)) {
            update_user_meta($user_id, 'surname', $data[2]);
        }

        try {
            $date = new DateTime(trim($data[6]));
        } catch (Exception $e) {
            echo $email . " ";
            echo $e->getMessage();
            return false;
        }
        $date_issue = $date->format('Y-m-d');
        $create_date = $date_issue;
        $series = trim($data[4]);
        $number = trim($data[5]);
        $productId = get_product_id_by_series($series);
        $product = get_post($productId);
        $insertData = [
            'user_id' => $user_id,
            'product_id' => $productId,
            'certificate_name' => $product->post_excerpt,
            'certificate_template_id' => (int)get_post_meta($productId, 'template_id', true),
            'graduate_first_name' => $data[1],
            'graduate_last_name' => $data[0],
            'graduate_surname' => $data[2],
            'date_issue' => $date_issue,
            'series' => $series,
            'number' => $number,
            'responsible_person' => 1,
            'create_date' => $create_date,
            'course_name' => get_post_meta($productId, 'course_name', true),
        ];
        Certificate::insertCertificate($insertData);
        dump($insertData);
        return true;
    }
    return false;
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
