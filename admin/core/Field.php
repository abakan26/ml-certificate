<?php


class Field
{
    public $name = '';
    public $code = '';
    /*Если $hide = 1 не показывать это поле в шаблоне*/
    public $hide = 0;
    public $position = null;
    public $size = null;
    public $text_align = '';
    public $font_size = 14;
    public $font_weight = '';
    public $color = '#000000';
    public $font_family = 'opensans';
    public $line_height = 14;
    public $example_text= '';

    public function __construct(
        string $name,
        string $code,
        array $params
    )
    {
        $this->name = $name;
        $this->code = $code;
        $this->hide = (int)$params['hide'];
        $this->position = (object)$params['position'];
        $this->size = (object)$params['size'];
        $this->text_align = $params['text_align'];
        $this->font_size = $params['font_size'];
        $this->font_weight = $params['font_weight'];
        $this->color = $params['color'];
        $this->font_family = $params['font_family'];
        $this->line_height = $params['line_height'];
        $this->example_text= $params['example_text'];
    }
}