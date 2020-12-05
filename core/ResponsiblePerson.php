<?php


class ResponsiblePerson
{
    public static function getResponsiblePersons()
    {
        return get_users([
            'role__in' => ['administrator', 'coach']
        ]);
    }
}