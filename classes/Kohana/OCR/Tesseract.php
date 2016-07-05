<?php

class Kohana_OCR_Tesseract extends OCR
{
    private $_executable;
    private $_temp_dir;
    private $_outname;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->_executable = $config['executable'];
        $this->_temp_dir = $config['temp_dir'];

        $this->_outname = sha1(uniqid(NULL, TRUE));
    }

    public function from($image)
    {
        $this->_image = $image;
        return $this;
    }

    public function cached($lifetime = NULL)
    {
        if ($lifetime === NULL)
        {
            $lifetime = Kohana::$cache_life;
        }

        $this->_is_cached = true;
        $this->_cache_life = $lifetime;
        return $this;
    }

    public function __toString()
    {
        return 'Tesseract OCR';
    }


    public function execute()
    {
        if ($this->_is_cached === true)
        {
            $cache_key = 'ocr-' . $this->_image;
            if (($result = Kohana::cache($cache_key, NULL, $this->_cache_life)) !== NULL)
            {
                return $result;
            }
            else
            {
                $result = $this->_shell_execute();
                Kohana::cache($cache_key, $result);
                return $result;
            }
        }

        // get data if caching is disabled
        $result = $this->_shell_execute();
        return $result;
    }

    private function _shell_execute()
    {
        if (!realpath($this->_image))
        {
            throw new OCR_Exception('File not found: ' . $this->_image);
        }

        $command = $this->_executable.' '.escapeshellarg($this->_image).' '.escapeshellarg($this->_temp_dir . DIRECTORY_SEPARATOR .$this->_outname);

        if (Kohana::$profiling)
        {
            $benchmark = Profiler::start("OCR", $command);
        }

        exec($command, $output, $returnval);
        $outfile = $this->_temp_dir . DIRECTORY_SEPARATOR . $this->_outname . '.txt';
        $contents = NULL;

        if ($returnval == 0)
        {
            if (realpath($outfile))
            {
                $contents = trim(file_get_contents($outfile));
                unlink($outfile);
            }
        }
        else
        {
            throw new OCR_Exception('Error while OCR image processing. External process exitcode: ' . $returnval);
        }

        if (isset($benchmark))
        {
            Profiler::stop($benchmark);
        }

        return $contents;
    }

}
