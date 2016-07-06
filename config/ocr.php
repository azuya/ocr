<?php

defined('SYSPATH') or die('No direct access allowed.');

return array(
    'default_driver' => 'tesseract',
    
    'tesseract' => array(
        'executable' => 'tesseract',
        'temp_dir' => APPPATH . 'tmp',
    ),
);