<?php


class Member
{
    public static function getMembersByFio(
        string $last_name,
        string $first_name,
        string $surname = '',
        $fields = 'all'
    ): array
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
            $meta_query[] = [
                'key' => 'surname',
                'value' => $surname
            ];
        }
        return get_users([
            'meta_query' => $meta_query,
            'fields' => $fields
        ]);
    }

    /**
     * Возвращает массив данных отфильтрованных пользователей
     * @param $ids int[]
     * @return Array
     */
    public static function getMembersDataByIds(array $ids): array
    {
        $users = [];
        foreach ($ids as $id) {
            // TODO get_userdata
            $user = get_user_by('ID', $id);
            $users[] = [
                'ID' => $id,
                'userName' => $user->user_login,
                'userLink' => get_edit_user_link($id),
                'lastName' => $user->last_name,
                'firstName' => $user->user_firstname,
                'surname' => get_user_meta($id, 'surname', true),
                'email' => $user->user_email,
            ];
        }
        return $users;
    }
}
