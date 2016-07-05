<?php

defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_OCR
{
    // OCR default
    public static $default = 'tesseract';

    /**
     * @var  array  OCR instances
     */
    public static $instances = array();

    protected $_image;
    protected $_output;
    protected $_is_cached = FALSE;
    protected $_cache_life = NULL;


    /**
     * Singleton pattern
     *
     * @return OCR
     */
    public static function instance($group = NULL)
    {
        // If there is no group supplied
        if ($group === NULL)
        {
            // Use the default setting
            $group = OCR::$default;
        }

        if (isset(OCR::$instances[$group]))
        {
            // Return the current group if initiated already
            return OCR::$instances[$group];
        }

        $config = Kohana::$config->load('ocr');

        if (!$config->offsetExists($group))
        {
            throw new Kohana_Exception(
            'Failed to load Kohana OCR group: :group', array(':group' => $group)
            );
        }

        $config = $config->get($group);

        // Create a new OCR type instance
        $class = 'OCR_' . ucfirst($config['driver']);
        OCR::$instances[$group] = new $class($config);

        // Return the instance
        return OCR::$instances[$group];
    }

    protected $_config = array();

    /**
     * Ensures singleton pattern is observed
     *
     * @param  array  $config  configuration
     */
    protected function __construct(array $config)
    {
        $this->config($config);
    }

    /**
     * Getter and setter for the configuration. If no argument provided, the
     * current configuration is returned. Otherwise the configuration is set
     * to this class.
     *
     *     // Overwrite all configuration
     *     $ocr->config(array('driver' => 'native', '...'));
     *
     *     // Set a new configuration setting
     *     $ocr->config('extocr', array(
     *          'foo' => 'bar',
     *          '...'
     *          ));
     *
     * @param   mixed    key to set to array, either array or config path
     * @param   mixed    value to associate with key
     * @return  mixed
     */
    public function config($key = NULL, $value = NULL)
    {
        if ($key === NULL)
            return $this->_config;

        if (is_array($key))
        {
            $this->_config = $key;
        } else
        {
            if ($value === NULL)
                return Arr::get($this->_config, $key);

            $this->_config[$key] = $value;
        }

        return $this;
    }

    /**
     * Overload the __clone() method to prevent cloning
     *
     * @return  void

     */
    final public function __clone()
    {
        throw new Kohana_Exception('Cloning of Kohana_OCR objects is forbidden');
    }

    abstract public function from($image);
    abstract public function cached($lifetime=NULL);
    abstract public function execute();

}

// End OCR
