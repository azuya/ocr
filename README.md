# OCR Image Processing for Kohana

This module is used to perform OCR processing.

## Requirements

You must install Tesseract OCR as external process.

## Example:

Simple execution:

```php
        $file = APPPATH . 'tmp'. DIRECTORY_SEPARATOR . 'test.png';
        $result = OCR::factory($file)
            ->execute();

        echo $result;
```

Execute using 1 day caching results:

```php
        $file = APPPATH . 'tmp'. DIRECTORY_SEPARATOR . 'test.png';
        $result = OCR::factory($file)
            ->cached(86400)
            ->execute();

        echo $result;
```

Execute and save text file:

```php
        $infile = APPPATH . 'tmp'. DIRECTORY_SEPARATOR . 'test.png';
        $outfile = APPPATH . 'tmp'. DIRECTORY_SEPARATOR . 'test.txt';
        $result = OCR::factory($infile)
            ->execute()
            ->save($outfile);
```

## Config

ocr.php

```php
return array(
    'default_driver' => 'tesseract',

    'tesseract' => array(
        'executable' => 'tesseract',
        'temp_dir' => APPPATH . 'tmp',
    ),
);
```

Note: You must create tmp dir in app path. This dir is used by external process to store
temporary data file.
