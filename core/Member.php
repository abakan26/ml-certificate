<?php


class Member
{
    public static function getMembersByFio(
        string $last_name,
        string $first_name,
        string $surname = '',
        $fields = 'all'
    )
    {
        $meta_query = [
            'relation' => 'AND',
            [
                'key' => 'first_name',
                'value' => $first_name
            ],
            [
                'key' => 'last_name',
                'value' => $last_name
            ],
        ];
        if (!empty($surname)) {
            $meta_query[] =   [
                'key' => 'surname',
                'value' => $surname
            ];
        }
        return get_users([
            'meta_query' => $meta_query,
            'fields' => $fields
        ]);
    }
}