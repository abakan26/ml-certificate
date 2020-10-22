<?php


class Data
{
    /**
     * @param array $data
     * @return string
     */
    public static function dataToString(array $data)
    {
        $updateData = '';
        foreach ($data as $key => $value) {
            if ($value === '') {
                $updateData .= "`{$key}` = NULL, ";
                continue;
            }
            $updateData .= "`{$key}` = '{$value}', ";
        }
        return substr($updateData, 0, -2);
    }
}