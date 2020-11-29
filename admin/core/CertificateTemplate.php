<?php


class CertificateTemplate
{
    public $id = 0;
    public $tableName = '';
    public $name = '';
    public $content = '';
    public $fields = [];
    public $img_src = '';
    const TABLE_NAME = 'memberlux_certificate_templates';

    public function __construct(int $id, string $name, stdClass $content)
    {
        global $wpdb;
        $this->tableName = $wpdb->prefix . self::TABLE_NAME;
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * @return stdClass|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        if (!empty($this->fields)) {
            return $this->fields;
        }
        $this->fields = (array)$this->getContent()->fields;
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getImgSrc(): string
    {
        if (!empty($this->img_src)) {
            return $this->img_src;
        }
        $this->img_src = wp_get_attachment_image_src($this->getContent()->attachment_id, 'full')[0];
        return $this->img_src;
    }

    public static function getTemplate(int $id): CertificateTemplate
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $data = $wpdb->get_row($wpdb->prepare("
            SELECT *
            FROM `$tableName`
            WHERE certificate_template_id = %d
        ", $id));
        return new CertificateTemplate($id, $data->name, json_decode($data->content));
    }


    public static function getCertificateTemplates(): array
    {
        global $wpdb;
        $ids = $wpdb->get_col("SELECT certificate_template_id FROM {$wpdb->prefix}" . self::TABLE_NAME);
        return array_map(function ($template_id) {
            return CertificateTemplate::getTemplate($template_id);
        }, $ids);
    }

    public static function getDownloadLink(int $certificateTemplateId)
    {
        return admin_url() . 'admin.php?page=ml_certificate_templates&download=' . $certificateTemplateId;
    }

    public static function getEditLink(int $certificateTemplateId)
    {
        return admin_url() . 'admin.php?page=ml_certificate_templates&certificate_id=' . $certificateTemplateId;
    }

    public static function createTemplate(string $name, int $attachment_id, array $fields)
    {
        global $wpdb;
        $sql = "INSERT INTO `{$wpdb->prefix}" . self::TABLE_NAME . "`
                (`name`, `content`)
                VALUES (%s, %s)
            ";
        $content = json_encode([
            'attachment_id' => (int)$attachment_id,
            'fields' => $fields
        ]);
        $data = [$name, $content];
        $wpdb->query($wpdb->prepare($sql, $data));
        return $wpdb->insert_id;
    }

    public static function updateTemplate(int $id, string $name, int $attachment_id, array $fields)
    {
        global $wpdb;
        $sql = "
            UPDATE `{$wpdb->prefix}" . self::TABLE_NAME . "`
            SET `name` = %s, `content` = %s
            WHERE certificate_template_id = %d
        ";
        $content = json_encode([
            'attachment_id' => (int)$attachment_id,
            'fields' => $fields
        ]);
        $data = [$name, $content, $id];
        $wpdb->query($wpdb->prepare($sql, $data));
    }

    public static function saveTemplates(string $action)
    {
        $certificate_id = isset($_POST['certificate_id']) ? intval($_POST['certificate_id']) : 0;
        $template_name = trim($_POST['name']);
        $attachment_id = intval($_POST['attachment_id']);
        $fields = [];
        if (empty($attachment_id)) {
            echo json_encode(['error' => 'Не выбрана картинка']);
            die();
        }
        if (!self::isUniqueName($template_name, $certificate_id)){
            echo json_encode(['error' => 'Шаблон с таким названием уже существует', 'error_input' => '#certificateName']);
            die();
        }
        foreach ($_POST['fields'] as $name => $field){
            $fieldForSave =  $field;
            if (!isset($field['hide'])){
                $fieldForSave['hide'] = 0;
            }
            $fields[$name] = $fieldForSave;
        }

        switch ($action) {
            case('create') :
                $certificate_id = CertificateTemplate::createTemplate(
                    $template_name,
                    $attachment_id,
                    $fields
                );
                echo json_encode([
                    'redirectUrl' => CertificateTemplate::getTemplateLink($certificate_id)
                ]);
                die();
            case('save'):
                if (empty($certificate_id)) {
                    echo json_encode(['error' => 'Ошибка в передачи id']);
                    die();
                }
                CertificateTemplate::updateTemplate(
                    $certificate_id,
                    $template_name,
                    $attachment_id,
                    $fields
                );
                echo json_encode(['success' => 'Настройки обновлены успешно!']);
                die();
            default:
                echo json_encode(['error' => 'Не указаный параметр action. Обратитесь к разработчику плагина']);
                die();
        }
    }

    public static function isUniqueName(string $name, int $certificate_id): bool
    {
        global $wpdb;
        $sql = "
            SELECT `certificate_template_id` FROM `{$wpdb->prefix}" . self::TABLE_NAME . "`
            WHERE `name` = %s AND `certificate_template_id` != %d
        ";
        $res = $wpdb->get_var($wpdb->prepare($sql, [$name, $certificate_id]));
        return empty($res);
    }

    public static function getTemplateLink(int $certificate_template_id): string
    {
       return admin_url() . 'admin.php?page=ml_certificate_templates&certificate_id=' . $certificate_template_id;
    }

    public static function getNameById(int $id){
        global $wpdb;
        return $wpdb->get_var("SELECT name FROM {$wpdb->prefix}" . self::TABLE_NAME . " 
        WHERE certificate_template_id = $id" );
    }

    public static function delete(int $id){
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE certificate_template_id = $id");
    }

    public static function isAccessToDelete(int $id)
    {
        $certificates = Certificate::query([
            'filter' => ['certificate_template_id' =>  $id]
        ]);
        $products = get_posts([
            'post_type' => 'product',
            'meta_query' => [
                [
                    'key' => 'template_id',
                    'value' => $id
                ]
            ]

        ]);
        return empty(count($products)) && empty($certificates['total']);
    }

}