<?php

defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_OCR
{

    // OCR default
    public static $default_driver = 'tesseract';
    public $file;
    protected $_is_cached = FALSE;
    protected $_cache_life = NULL;

    public static function factory($file, $driver = NULL)
    {
        if ($driver === NULL)
        {
            // Use the driver from configuration file or default one
            $configured_driver = Kohana::$config->load('ocr.default_driver');
            $driver = ($configured_driver) ? $configured_driver : OCR::$default_driver;
        }

        // Set the class name
        $class = 'OCR_' . $driver;

        return new $class($file);
    }

    protected function __construct($file)
    {
        try
        {
            // Get the real path to the file
            $file = realpath($file);

            // Get the image information
            $info = getimagesize($file);
        } catch (Exception $e)
        {
            // Ignore all errors while reading the image
        }

        if (empty($file) OR empty($info))
        {
            throw new OCR_Exception('Not an image or invalid image: :file', array(':file' => Debug::path($file)));
        }

        // Store the image information
        $this->file = $file;
    }

    abstract public function cached($lifetime = NULL);
    abstract public function execute();
    abstract public function save($file);
}

// End OCR
