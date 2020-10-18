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
            $data->certificate_name,
            (int)$data->user_id,
            (int)$data->certificate_template_id,
            (int)$data->product_id,

            $data->graduate_first_name,
            $data->graduate_last_name,
            $data->graduate_surname,
            $data->date_issue,
            $data->series,
            $data->number,
            $data->responsible_person,
            $data->create_date,
            $data->course_name
        );
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
        foreach ($wpdb->get_col($sql) as $id){
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
}