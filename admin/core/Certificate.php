<?php


class Certificate
{
    const TABLE_NAME = 'memberlux_certificate';
    public $id = 0;
    public $user_id = 0;
    public $certificate_template_id = 0;
    public $product_id = 0;
    public $certificate_name = '';
    public $graduate_first_name = '';
    public $graduate_last_name = '';
    public $graduate_surname = '';
    public $date_issue = '';
    public $series = '';
    public $number = '';
    public $responsible_person = '';
    public $create_date = '';
    public $course_name = '';

    public function __construct(
        int $id,
        string $certificate_name,
        int $user_id,
        int $certificate_template_id,
        int $product_id,
        string $graduate_first_name,
        string $graduate_last_name,
        string $graduate_surname,
        string $date_issue,
        string $series,
        string $number,
        string $responsible_person,
        string $create_date,
        string $course_name
    )
    {
        global $wpdb;
        $this->id = $id;
        $this->certificate_name = $certificate_name;
        $this->user_id = $user_id;
        $this->certificate_template_id = $certificate_template_id;
        $this->product_id = $product_id;
        $this->graduate_first_name = $graduate_first_name;
        $this->graduate_last_name = $graduate_last_name;
        $this->graduate_surname = $graduate_surname;
        $this->date_issue = $date_issue;
        $this->series = $series;
        $this->number = $number;
        $this->responsible_person = $responsible_person;
        $this->create_date = $create_date;
        $this->course_name = $course_name;
    }

    public static function create(
        int $user_id,
        string $certificate_name,
        int $certificate_template_id,
        int $product_id,
        string $graduate_first_name,
        string $graduate_last_name,
        string $graduate_surname,
        string $date_issue,
        string $series,
        string $responsible_person,
        string $create_date,
        string $course_name
    ): int
    {
        global $wpdb;
        $number = Certificate::generateCertificateNumber($user_id);
        $sql = "INSERT INTO `$wpdb->prefix" . self::TABLE_NAME . "`
         (`certificate_name`, `user_id`, `certificate_template_id`, `product_id`, `graduate_first_name`, `graduate_last_name`, `graduate_surname`,
         `date_issue`, `series`, `number`, `responsible_person`, `create_date`, `course_name`)
          VALUES (%s, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s)";
        $prepare = $wpdb->prepare($sql, [
            $certificate_name,
            $user_id,
            $certificate_template_id,
            $product_id,
            $graduate_first_name,
            $graduate_last_name,
            $graduate_surname,
            $date_issue,
            $series,
            $number,
            $responsible_person,
            $create_date,
            $course_name,
        ]);
        $wpdb->query($prepare);
        return $wpdb->insert_id;
    }

    public static function getCertificate($id)
    {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE certificate_id = $id";
        $data = $wpdb->get_row($sql);
        return new Certificate(
            (int)$data->certificate_id,
            $data->certificate_name ? $data->certificate_name : 'Название сертификата',
            (int)$data->user_id,
            (int)$data->certificate_template_id,
            (int)$data->product_id,

            $data->graduate_first_name ? $data->graduate_first_name : '',
            $data->graduate_last_name ? $data->graduate_last_name : '',
            $data->graduate_surname ? $data->graduate_surname : '',
            $data->date_issue,
            $data->series ? $data->series : '',
            $data->number,
            $data->responsible_person,
            $data->create_date,
            $data->course_name ? $data->course_name : ''
        );
    }

    public static function update(int $certificate_id, array $fields = [])
    {
        global $wpdb;
        $tablename = $wpdb->prefix . self::TABLE_NAME;
        $setData = Data::dataToString(self::cleanKey($fields));
        $wpdb->query("UPDATE $tablename SET $setData WHERE `certificate_id` = $certificate_id");
    }

    public static function delete(int $certificate_id)
    {
        global $wpdb;
        $tablename = $wpdb->prefix . self::TABLE_NAME;
        $wpdb->query("DELETE FROM $tablename WHERE `certificate_id` = $certificate_id");
        //TODO return bool
    }

    public static function cleanKey(array $fields): array
    {
        global $wpdb;
        $tablename = $wpdb->prefix . self::TABLE_NAME;
        $keys = $wpdb->get_col("SHOW COLUMNS FROM $tablename");
        return array_filter($fields, function ($key) use ($keys) {
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    public static function generateCertificateNumber(int $user_id): string
    {
        return self::numberFormat($user_id, 5);
    }

    public static function numberFormat($digit, $width)
    {
        while (strlen($digit) < $width)
            $digit = '0' . $digit;
        return $digit;
    }

    public static function getCustomerCertificates($userId)
    {
        global $wpdb;
        $certificates = [];
        $sql = "SELECT certificate_id FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE user_id = $userId";
        foreach ($wpdb->get_col($sql) as $id) {
            $certificates[] = Certificate::getCertificate($id);
        }
        return $certificates;
    }

    public static function isCustomerCertificate(int $userId, int $certificateId)
    {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}" . self::TABLE_NAME . " 
                WHERE user_id = $userId AND certificate_id = $certificateId";
        return !empty($wpdb->get_var($sql));
    }

    public static function getCertificatesByProductId(int $productId, $flag = 'object', $excludeRP1 = false): array
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $certificateIds = $wpdb->get_col("SELECT `certificate_id`
            FROM $tableName
            WHERE `product_id` = $productId" . ($excludeRP1 ? ' AND `responsible_person` != 1' : '')
        );
        if ($flag = 'ids') {
            return $certificateIds;
        }
        return array_map(function ($id) {
            return self::getCertificate($id);
        }, $certificateIds);
    }

    public static function insertCertificate(array $params): int
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $sql = "INSERT INTO `$tableName`
         (
          `user_id`, `product_id`, `certificate_name`, `certificate_template_id`,
          `graduate_first_name`, `graduate_last_name`, `graduate_surname`,
          `date_issue`, `series`, `number`, `responsible_person`,
          `create_date`, `course_name`)
          VALUES (
            %d, %d, %s, %d,
            %s, %s, %s,
            %s, %s, %s, %d,
            %s, %s
          )";
        $prepare = $wpdb->prepare($sql, [
            $params['user_id'], $params['product_id'], $params['certificate_name'], $params['certificate_template_id'],
            $params['graduate_first_name'], $params['graduate_last_name'], $params['graduate_surname'],
            $params['date_issue'], $params['series'], $params['number'], $params['responsible_person'],
            $params['create_date'], $params['course_name'],
        ]);
        $wpdb->query($prepare);
        return $wpdb->insert_id;
    }

    public static function query($params)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $perPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $orderBy = isset($params['order_by']) ? $params['order_by'] : 'user_login';
        $order = isset($params['order']) ? strtoupper($params['order']) : 'ASC';
        $filter = isset($params['filter']) ? (array)$params['filter'] : null;
        $email = isset($params['email']) ? $params['email'] : "";

        $whereConditions = [];
        foreach ($filter as $key => $value) {
            if ((is_string($value) || is_numeric($value)) && !empty($value)) {
                $whereConditions[] = " `$key` = " . esc_sql($value);
            } elseif (is_array($value)) {
                if (!empty($value['from']) && !empty($value['to'])) {
                    $whereConditions[] = " ( `$key` BETWEEN '" . esc_sql($value['from']) . "' AND '" . esc_sql($value['to']) . "')";
                } elseif (!empty($value['from'])) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value['from']) . "'";
                } elseif (!empty($value['to'])) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value['to']) . "'";
                }
            } elseif (is_object($value)) {
                if (!empty($value->from) && !empty($value->to)) {
                    $whereConditions[] = " ( `$key` BETWEEN '" . esc_sql($value->from) . "' AND '" . esc_sql($value->to) . "')";
                } elseif (!empty($value->from)) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value->from) . "'";
                } elseif (!empty($value->to)) {
                    $whereConditions[] = " `$key` = '" . esc_sql($value->to) . "'";
                }

            }
        }

        //construct query
        $count_query = 'SELECT COUNT(*)';
        $base_query = "SELECT certificate.*, users.`user_login`, users.`user_email`";
        $inner_query = " FROM $tableName AS certificate
            LEFT JOIN $wpdb->users AS users ON certificate.user_id = users.ID";
        if (!empty($email)) {
            $whereConditions = [];
            $whereConditions[] = " users.`user_email` LIKE '%$email%'";
        }
        $where = empty($whereConditions) ? '' : ' WHERE' . implode(' AND', $whereConditions);
        $order = " ORDER BY $orderBy $order";
        $sql_count_query = $count_query . $inner_query . $where . $order;
        $total = $wpdb->get_var($sql_count_query);
        $totalPages = (int)ceil($total / $perPage);
        if (!isset($params['page_num']) || empty($params['page_num'])) {
            $pageNum = 1;
        } else {
            $pageNum = $params['page_num'] > $totalPages ? $totalPages : $params['page_num'];
        }
        $start = $perPage * ($pageNum - 1);
        $limit = " LIMIT {$start}, {$perPage}";
        $sql_query = $base_query . $inner_query . $where . $order . $limit;
        return [
            'result' => $wpdb->get_results($sql_query),
            'page_num' => $pageNum,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'total' => intval($total),
            'sql' => $sql_query
        ];
    }
}