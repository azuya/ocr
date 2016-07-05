# OCR Image Processing for Kohana

This module is used to perform OCR processing.

## Requirements

You must install Tesseract OCR as external process.

## Example:

```php
        $result = OCR::instance()
            ->from(APPPATH . 'tmp'. DIRECTORY_SEPARATOR . 'test.png')
            ->cached()
            ->execute();

        echo Debug::vars($result);
```

## Config

ocr.php

```php
return array(
    'tesseract' => array(
        'driver' => 'tesseract',
        'executable' => 'tesseract',
        'temp_dir' => APPPATH . 'tmp',
    ),
);
```

Note: You must create tmp dir in app path. This dir is used by external process to store
temporary data file.
