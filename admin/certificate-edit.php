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
    $handle = fopen(__DIR__ . "/import-sert.csv", "r");
    if ($handle === false) {
        dump(false);
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
        if (!empty($user_data[$key]) && $user_data[$key] !== $table_data[$key]){
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
        do_import($data);
        return;
    }
    if (is_coincidence($user_data, $table_data)){
        do_import($data);
        return;
    }
    $no_coincidence = array_reduce(array_keys($user_data), function ($carry, $key) use ($user_data, $table_data){
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
    if ($user = get_user_by('email', $email)) {
        handler_existing_user($user, $data);
        return;
    }
    write_not_exist($data);
}
echo 'sds';

// $offset = get_n_csv('', 0, 2969, 'import_handler');
$time_end = microtime(true);
dump($time_end - $time_start);