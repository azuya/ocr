<?php

class Kohana_OCR_Tesseract extends OCR
{
    private $_executable;
    private $_temp_dir;
    private $_outname;

    private $_result = '';

    /**
     * Create a new [OCR_Tesseract].
     *
     * @param   string
     * @return  OCR_Tesseract
     */

    public function __construct($file)
    {
        parent::__construct($file);

        $this->_executable = Kohana::$config->load('ocr.tesseract')['executable'];
        $this->_temp_dir = Kohana::$config->load('ocr.tesseract')['temp_dir'];

        $this->_outname = sha1(uniqid(NULL, TRUE));
    }

    /**
     * Set output caching.
     *
     * @param   int
     * @return  $this
     */

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

    /**
     * Get OCR results as string.
     *
     * @return  string
     */

    public function __toString()
    {
        return $this->_result;
    }


    /**
     * Execute OCR.
     *
     * @return  $this
     */

    public function execute()
    {
        if ($this->_is_cached === true)
        {
            $cache_key = 'ocr-' . $this->file;
            if (($this->_result = Kohana::cache($cache_key, NULL, $this->_cache_life)) !== NULL)
            {
                return $this;
            }
            else
            {
                $this->_result = $this->_shell_execute();
                Kohana::cache($cache_key, $this->_result);
                return $this;
            }
        }

        // get data if caching is disabled
        $this->_result = $this->_shell_execute();
        return $this;
    }

    /**
     * Save output tofile.
     *
     * @param   string
     * @return  $this
     * @throws  OCR_Exception
     */

    public function save($file)
    {
        try
        {
            file_put_contents($file, $this->_result, LOCK_EX);
            return $this;
        }
        catch (Exception $ex)
        {
            throw new OCR_Exception('Error on saving results. Message: ' . $e->getMessage());
        }
    }

    /**
     * Internal Shell Execute.
     *
     * @return  string
     * @throws  OCR_Exception
     */

    private function _shell_execute()
    {
        $command = $this->_executable.' '.escapeshellarg($this->file).' '.escapeshellarg($this->_temp_dir . DIRECTORY_SEPARATOR .$this->_outname);

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
