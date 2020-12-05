<?php


class ImportedRow
{
    public $email;
    public $last_name;
    public $first_name;
    public $surname;
    public $series;
    public $number;
    public $date;

    public function __construct($args = [])
    {
        $defaultArgs = [
            'email'      => '',
            'last_name'  => '',
            'first_name' => '',
            'surname'    => '',
            'series'     => '',
            'number'     => '',
            'date'       => '',
        ];
        $args = array_merge($defaultArgs, $args);
        $this->email = trim($args['email']);
        $this->last_name = trim($args['last_name']);
        $this->first_name = trim($args['first_name']);
        $this->surname = trim($args['surname']);
        $this->series = trim($args['series']);
        $this->number = trim($args['number']);
        $this->date = trim($args['date']);
    }
}