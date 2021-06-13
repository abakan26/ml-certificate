<?php


class FilterMembers
{
    const FILE_NAME = 'users.xls';
    public $includeProductIds;
    public $includeCategory;
    public $excludeProductIds;
    public $excludeCategory;
    public $includeCategoryIsCourse;
    public $excludeCategoryIsCourse;
    public $date;
    public $datePeriod;
    /**
     * @var mixed|string
     * 'date_start' or 'date_end'
     */
    public $wpmLevel;

    public function __construct(
        $includeProduct = ['ids' => [], 'catId' => 0],
        $excludeProduct = ['ids' => [], 'catId' => 0],
        $date = '',
        $datePeriod = '',
        $wpmLevel = ''
    )
    {
        $this->includeProductIds = $includeProduct['ids'];
        $this->includeCategory = $includeProduct['catId'];
        $this->excludeProductIds = $excludeProduct['ids'];
        $this->excludeCategory = $excludeProduct['catId'];
        $this->includeCategoryIsCourse = Course::isCourse($this->includeCategory);
        $this->excludeCategoryIsCourse = Course::isCourse($this->excludeCategory);
        $this->date = $date;
        $this->datePeriod = $datePeriod;
        $this->wpmLevel = $wpmLevel;
    }

    public function getMembers(): array
    {
        global $wpdb;
        $memberIds = [];
        $includes = [];

        if (!empty($this->includeProductIds)) {
            $includes = $this->includeCategoryIsCourse
                ? $this->getIncludeMemberIdsByCertificate()
                : $this->getIncludeMemberIdsByWPMLevels();
        }

        if (empty($this->excludeProductIds)) {
            return $this->getMembersDataByIds($includes);
        }

        if (empty($includes)) {
            $memberIds = $wpdb->get_col("SELECT `ID` FROM $wpdb->users");
        } else {
            $memberIds = $this->excludeCategoryIsCourse
                ? $this->filterExcludeMemberIdsByCertificate($includes)
                : $this->filterExcludeMemberIdsByWPMLevels($includes);
        }

        return $this->getMembersDataByIds($memberIds);
    }

    /**
     * Возвращает массив данных отфильтрованных пользователей
     * @param $ids int[]
     * @return Array
     */
    public function getMembersDataByIds(array $ids): array
    {
        return Member::getMembersDataByIds($ids);
    }

    /**
     * Возвращает массив из ID пользователей, у которых есть сертификаты по все "выбранным" курсам
     * @return int[]
     */
    private function getIncludeMemberIdsByCertificate(): array
    {
        global $wpdb;
        $certificateTable = $wpdb->prefix . Certificate::TABLE_NAME;
        $count = count($this->includeProductIds);
        $productIn = implode(', ', $this->includeProductIds);
        //  AND certificate.`date_issue`
        $where = "WHERE certificate.`product_id` IN ($productIn)";
        if (!empty($this->date)) {
            $sign = $this->datePeriod === 'before' ? '<' : '>';
            $where .= " AND certificate.`date_issue` $sign '$this->date'";
        }
        $sql = "
            SELECT `ID`
            FROM $wpdb->users AS users
            INNER JOIN `$certificateTable` AS certificate ON certificate.`user_id` = users.`ID`
            $where
            GROUP BY users.`ID`
            HAVING COUNT(DISTINCT certificate.`certificate_id`) = $count;
        ";
        return $wpdb->get_col($sql);
    }

    private function getIncludeMemberIdsByWPMLevels(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'memberlux_term_keys';
        $wpmLevels = array_map(['Course', 'getWPMLevel'], $this->includeProductIds);
        $count= count($wpmLevels);
        $wpmLevelsIn = implode(', ', $wpmLevels);
        $where = "WHERE levels.`term_id` IN ($wpmLevelsIn)";
        if (!empty($this->date)) {
            $sign = $this->datePeriod === 'before' ? '<' : '>';
            $where .= " AND levels.`{$this->wpmLevel}` $sign '$this->date'";
        }
        $sql = "
            SELECT users.`ID`
            FROM $wpdb->users AS users
            INNER JOIN `$table` AS levels ON levels.`user_id` = users.`ID`
            $where
            GROUP BY users.`ID`
            HAVING COUNT(DISTINCT levels.`id`) = $count;
        ";
        return $wpdb->get_col($sql);
    }

    private function filterExcludeMemberIdsByWPMLevels($ids): array
    {
        $wpmLevels = array_map(['Course', 'getWPMLevel'], $this->excludeProductIds);
        $wpmLevelsEx = implode(', ', $wpmLevels);
        $countEx = count($wpmLevels);
        return array_filter($ids, function ($id) use ($wpmLevelsEx, $countEx) {
            global $wpdb;
            $table = $wpdb->prefix . 'memberlux_term_keys';
            $sql = "SELECT id FROM `$table`
                WHERE `user_id` = $id AND `term_id` IN($wpmLevelsEx)
                GROUP BY `user_id` 
                HAVING COUNT(DISTINCT `term_id`) = $countEx";
            return !count($wpdb->get_results($sql));
        });
    }

    private function filterExcludeMemberIdsByCertificate($ids): array
    {
        $productEx = implode(', ', $this->excludeProductIds);
        $countEx = count($this->excludeProductIds);
        return array_filter($ids, function ($id) use ($productEx, $countEx) {
            global $wpdb;
            $certificateTable = $wpdb->prefix . Certificate::TABLE_NAME;
            $sql = "SELECT certificate_id FROM `$certificateTable`
                WHERE `user_id` = $id AND `product_id` IN($productEx)
                GROUP BY `user_id` 
                HAVING COUNT(DISTINCT `product_id`) = $countEx";
            return !count($wpdb->get_results($sql));
        });
    }

    public static function saveUserIds($ids):string
    {
        file_put_contents(
            PLUGIN_PATH . '/uploads/filter-ids.json',
            json_encode($ids)
        );
        return plugin_dir_url(PLUGIN_PATH . '/excelGenerator.php') . 'excelGenerator.php';
    }

    public static function getExcelFile()
    {
        require_once dirname(__DIR__) . '/libs/vendor/autoload.php';
        $ids = explode(',', json_decode(file_get_contents(dirname(__DIR__) . '/uploads/filter-ids.json')));
        $document = new \PHPExcel();
        try {
            $sheet = $document->setActiveSheetIndex(0);
            $column = 0;
            foreach (['ID', 'Логин', 'Фамилия', 'Имя', 'Отчество', 'email'] as $heading) {
                $sheet->setCellValueByColumnAndRow($column++, 1, $heading);
            }
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $heading) {
                $sheet->getColumnDimension($heading)->setAutoSize(true);
            }
            $startLine = 2;
            $columnPosition = 0;
            $users = Member::getMembersDataByIds($ids);
            foreach ($users as $key => $user) {
                $currentColumn = $columnPosition;
                foreach ($user as $field => $value) {
                    if ($field === 'userLink') continue;
                    $sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $value);
                    $currentColumn++;
                }
                $startLine++;
            }

            $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel5');
            $objWriter->save('php://output');
        } catch (Exception $error) {
            var_dump($error->getMessage());
        }
    }

}
