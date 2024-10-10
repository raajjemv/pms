<?php

return [
    'mode'                     => '',
    'format'                   => 'A4',
    'default_font_size'        => '10',
    'default_font'             => 'sans-serif',
    'margin_left'              => 1,
    'margin_right'             => 1,
    'margin_top'               => 1,
    'margin_bottom'            => 1,
    'margin_header'            => 1,
    'margin_footer'            => 1,
    'orientation'              => 'P',
    'title'                    => 'Laravel mPDF',
    'subject'                  => '',
    'author'                   => '',
    'watermark'                => '',
    'show_watermark'           => false,
    'show_watermark_image'     => false,
    'watermark_font'           => 'sans-serif',
    'display_mode'             => 'fullpage',
    'watermark_text_alpha'     => 0.1,
    'watermark_image_path'     => '',
    'watermark_image_alpha'    => 0.2,
    'watermark_image_size'     => 'D',
    'watermark_image_position' => 'P',
    'auto_language_detection'  => false,
    'temp_dir'                 => storage_path('app'),
    'pdfa'                     => false,
    'pdfaauto'                 => false,
    'use_active_forms'         => false,
    'custom_font_dir'          => public_path('fonts/'),
    'custom_font_data'         => [
        'arial' => [
            'R'  => 'Arial.ttf',
        ],
        'roboto' => [
            'R'  => 'Roboto/Roboto-Regular.ttf',
        ]
    ]
];
