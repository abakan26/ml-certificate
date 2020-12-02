<?php
$FIELDS = [
    new Field(
        'ФИО',
        'name', /*ВАЖНО!!! не менять код поля. Код поля должен быть уникальным*/
        [
            'hide' => 0,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 357,
                'left' => 0,
            ],
            'size' => [
                'width' => 793
            ],
            'text_align' => 'center',
            'font_size' => 22,
            'font_weight' => 'bold',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 28,
            'example_text' => 'Иванов Иван Иванович',
        ]
    ),
    new Field(
        'Дата',
        'date',
        [
            'hide' => 0,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 969,
                'left' => 382,
            ],
            'size' => [
                'width' => 85
            ],
            'text_align' => 'center',
            'font_size' => 14,
            'font_weight' => 'normal',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 24,
            'example_text' => '18.05.2020',
        ]
    ),
    new Field(
        'Серия',
        'series',
        [
            'hide' => 0,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 970,
                'left' => 593,
            ],
            'size' => [
                'width' => 36
            ],
            'text_align' => 'center',
            'font_size' => 14,
            'font_weight' => 'normal',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 24,
            'example_text' => '142',
        ]
    ),
    new Field(
        'Номер',
        'number',
        [
            'hide' => 0,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 969,
                'left' => 657,
            ],
            'size' => [
                'width' => 56
            ],
            'text_align' => 'center',
            'font_size' => 14,
            'font_weight' => 'normal',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 24,
            'example_text' => '00005',
        ]
    ),
    new Field(
        'Название материала',
        'course',
        [
            'hide' => 0,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 323 ,
                'left' => 0,
            ],
            'size' => [
                'width' => 793
            ],
            'text_align' => 'center',
            'font_size' => 20,
            'font_weight' => 'bold',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 24,
            'example_text' => 'Грудное вскармливание',
        ]
    ),
    new Field(
        'Доп поле 1',
        'field1',
        [
            'hide' => 1,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 288,
                'left' => 0,
            ],
            'size' => [
                'width' => 793
            ],
            'text_align' => 'center',
            'font_size' => 20,
            'font_weight' => 'normal',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 24,
            'example_text' => 'прослушала вебинар',
        ]
    ),
    new Field(
        'Доп поле 2',
        'field2',
        [
            'hide' => 1,  /*Скрывать это поле? Если hide = 1, то поле будет скрыто.*/
            'position' => [
                'top' => 500,
                'left' => 0,
            ],
            'size' => [
                'width' => 793
            ],
            'text_align' => 'center',
            'font_size' => 20,
            'font_weight' => 'normal',
            'color' => '#000000',
            'font_family' => 'opensans',
            'line_height' => 24,
            'example_text' => 'Участниками было прослушано',
        ]
    ),
];