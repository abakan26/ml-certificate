<?php


class ImportHandler
{
    private $responsible_person = 1;
    private $filename;
    private $delimiter = ',';
    private $length = 300;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function import()
    {
        $count = 0;
        $handle = fopen($this->filename, "r");
        if ($handle === false) {
            print_r("Не удалось открыть файл $this->filename");
            return false;
        }
        while ($row = fgetcsv($handle, $this->length, $this->delimiter)) {
            if ($this->import_certificate($row)) {
                $count++;
            } else {
                print "ОШИБКА: ";
            }
        }
        fclose($handle);
        echo '<br><br>';
        echo '******************************';
        print_r("Имортированно $count записей");
    }

    public function import_certificate($data)
    {
        $email = trim($data[3]);
        $user = get_user_by('email', $email);
        $notes = [];
        if (!$user) {
            print_r('Позователя с указанным email не существует');
            print_r($data);
            return false;
        }
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
        $filtered = HandlerGrouping::usersFilter($importRow, $member);
        if ($filtered['status'] === 'fail') {
            $notes = [get_edit_user_link($member->id)];
            $fields = ['last_name', 'first_name', 'surname'];
            foreach ($fields as $field) {
                $notes[] = $member->{$field};
            }
            print_r($data);
            print_r($notes);
            return false;
        }
        if ($this->need_update($filtered)) {
            $this->update_user($importRow, $filtered, $member);
        }
        try {
            $date = new DateTime($importRow->date);
        } catch (Exception $e) {
            echo $email . " ";
            echo $e->getMessage();
            return false;
        }
        $date_issue = $date->format('Y-m-d');
        $create_date = $date_issue;
        $series = $importRow->series;
        $number = $importRow->number;
        $productId = $this->get_product_id_by_series($series);
        $product = get_post($productId);
        $insertData = [
            'user_id' => $member->id,
            'product_id' => $productId,
            'certificate_name' => $product->post_excerpt,
            'certificate_template_id' => (int)get_post_meta($productId, 'template_id', true),
            'graduate_first_name' => $importRow->first_name,
            'graduate_last_name' => $importRow->last_name,
            'graduate_surname' => $importRow->surname,
            'date_issue' => $date_issue,
            'series' => $importRow->series,
            'number' => $importRow->number,
            'responsible_person' => $this->responsible_person,
            'create_date' => $create_date,
            'course_name' => get_post_meta($productId, 'course_name', true),
        ];
        if ($insert_id = Certificate::insertCertificate($insertData)) {
            print_r("Импортирован успешно: $insert_id");
            echo '<br><br>';
            return true;
        }
        print_r("Не удалось создать сертификат пользователя $member->id");
        print_r($data);
        echo '<br><br>';
        return false;
    }

    private function need_update($filtered)
    {
        return !empty(array_filter($filtered['results'], function ($arr) {
            return $arr['need_update'];
        }));
    }

    private function get_product_id_by_series($series)
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

    private function update_user($importRow, $filtered, $member)
    {
        foreach ($filtered['results'] as $result) {
            if ($result['need_update']) {
                if (
                update_user_meta($member->id, $result['field'], $importRow->{$result['field']})
                ) {
                    echo "У пользователя $member->id было обновлено поле: " . $result['field'];
                } else {
                    echo "У пользователя $member->id не удалось обновить поле: " . $result['field'];
                }

            }
        }
    }
}