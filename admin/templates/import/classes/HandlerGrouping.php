<?php


class HandlerGrouping
{
    public $ready_to_import_filename;
    public $exist_fail_filename;
    public $no_exists;

    public function __construct($args)
    {
        $defaultArgs = [
            'ready_to_import_filename' => 'import-exists.csv',
            'exist_fail_filename' => 'user-exists-fail.csv',
            'no_exists' => 'no-exists.csv'
        ];
        $args = array_merge($defaultArgs, $args);
        $this->ready_to_import_filename = $args['ready_to_import_filename'];
        $this->exist_fail_filename = $args['exist_fail_filename'];
        $this->no_exists = $args['no_exists'];
    }

    public function userFilterHandler(array $data)
    {
        $email = trim($data[3]);
        $notes = [];
        if ($user = get_user_by('email', $email)) {
            $importRow = new ImportedRow([
                'email' => $data[3],
                'last_name' => $data[0],
                'first_name' => $data[1],
                'surname' => $data[2],
                'series' => $data[4],
                'number' => $data[5],
                'date' => $data[6],
            ]);
            $member = new AcademyMember($user);
            $filtered = self::usersFilter($importRow, $member);
            if ($filtered['status'] === 'fail') {
                $notes = [get_edit_user_link($member->id)];
                $fields = ['last_name', 'first_name', 'surname'];
                foreach ($fields as $field) {
                    $notes[] = $member->{$field};
                }
                $this->write_result($this->exist_fail_filename, $data, $notes);
                return true;
            }
            $this->write_result($this->ready_to_import_filename, $data);
            return true;
        }
        $this->write_result($this->no_exists, $data);
        return true;
    }

    public function findDuplicateHandler($filename, $first, $second)
    {
        $duplicates = $this->findDuplicate($filename, $first, $second, 3);
        //do action with duplicates row by $first and $second fields
    }


    public static function usersFilter(ImportedRow $tableRow, AcademyMember $user, $need_update)
    {
        $results = [];
        if ($need_update) {
            return [
                'status' => 'ok',
                'results' => [
                    [
                        'field' => 'last_name',
                        'need_update' => true
                    ],
                    [
                        'field' => 'first_name',
                        'need_update' => true
                    ],
                    [
                        'field' => 'surname',
                        'need_update' => true
                    ],
                ]
            ];
        }
        foreach (['last_name', 'first_name', 'surname'] as $field) {
            $results[] = self::compareFields($tableRow->$field, $user->$field, $field);
        }
        $status = empty(array_filter($results, function ($arr) {
            return !is_array($arr);
        })) ? 'ok' : 'fail';
        return ['status' => $status, 'results' => $results];
    }

    public static function compareFields(string $table, $academy, $name)
    {
        if (empty($table)) {
            return [
                'field' => $name,
                'need_update' => false
            ];
        }
        if (empty($academy)) {
            return [
                'field' => $name,
                'need_update' => true
            ];
        }
        if ($table === $academy) {
            return [
                'field' => $name,
                'need_update' => false
            ];
        }

        return false;
    }

    private function findDuplicate($filename, $first, $second, $unique_field, $delimiter = ',')
    {
        $matches = [];
        $count = 0;
        $result = [];
        $handle = fopen($filename, "r");
        if ($handle === false) {
            return false;
        }
        while ($row = fgetcsv($handle, 300, $delimiter)) {
            $key = $row[$first] . $row[$second];
            $result[$key][] = $row;
        }
        fclose($handle);
        foreach ($result as $key => $group) {
            $unique_group = self::unique_group($group, $unique_field);
            if (count($unique_group) > 1) {
                $count++;
                $matches[] = $unique_group;
            }
        }
        return ['matches' => $matches, 'count' => $count];
    }

    private function write_result($filename, $data, $notes = [])
    {
        $fields = array_merge($data, $notes);
        $fp = fopen(__DIR__ . "/$filename", 'a');
        fputcsv($fp, $fields);
        fclose($fp);
    }

    public static function unique_group($group, $unique_field)
    {
        $groupByField = [];
        foreach ($group as $item) {
            $groupByField[$item[$unique_field]] = $item;
        }
        return array_values($groupByField);
    }
}
