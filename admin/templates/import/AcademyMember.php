<?php


class AcademyMember
{
    public $id;
    public $email;
    public $last_name;
    public $first_name;
    public $surname;

    public function __construct(WP_User $user)
    {
        $this->id = $user->ID;
        $this->email = trim($user->user_email);
        $this->last_name  = trim($user->last_name);
        $this->first_name = trim($user->first_name);
        $this->surname    = trim(get_user_meta($this->id, 'surname', true));
    }
}