<?php

return [

    /*
    |----------------------------------------------------------------------
    | Settings
    |----------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,  // Throw an Exception on warnings from dompdf

    'public_path' => null,  // Override the public path if needed

    'convert_entities' => true,

    'options' => [
        // Path to DOMPDF font directory
        'font_dir' => storage_path('fonts'),

        // Path to font cache directory
        'font_cache' => storage_path('fonts'),

        // Temporary directory for downloaded remote images
        'temp_dir' => sys_get_temp_dir(),

        // "chroot": Prevents dompdf from accessing system files
        'chroot' => realpath(base_path()),

        // Allowed protocols for images, CSS, etc.
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        'log_output_file' => null, // Log output file if you need it

        // Enable or disable font subsetting
        'enable_font_subsetting' => false,

        // PDF rendering backend to use (PDFLib, CPDF, GD)
        'pdf_backend' => 'CPDF',

        // Default media type for HTML rendering
        'default_media_type' => 'screen',

        // Default paper size (a4, letter, etc.)
        'default_paper_size' => 'a4',

        // Default paper orientation (portrait, landscape)
        'default_paper_orientation' => 'portrait',

        // Default font family
        'default_font' => 'serif',

        // DPI setting for images and fonts
        'dpi' => 96,

        // Enable embedded PHP (set to false for security)
        'enable_php' => false,

        // Enable inline JavaScript
        'enable_javascript' => true,

        // Enable remote file access
        'enable_remote' => false,

        // List of allowed remote hosts
        'allowed_remote_hosts' => null,

        // Font height ratio adjustment
        'font_height_ratio' => 1.1,

        // Enable HTML5 parser (enabled by default in dompdf 2.x)
        'enable_html5_parser' => true,
    ],

];
