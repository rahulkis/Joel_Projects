<?php

namespace AsposeWords;

use Aspose\Words\Model\Requests\ConvertDocumentRequest;
use Aspose\Words\ApiException;
use SplFileObject;


class ImportEngine
{

    public function __construct($inputfile)
    {
        $this->inputfile = $inputfile;
    }

    public function getHtml()
    {
        return file_get_contents($this->convertedFilePath);
    }

    public function convert()
    {
        $file = null;
        try {
            $file = new SplFileObject($this->inputfile);
            $req = new ConvertDocumentRequest($file, "HTML", null);
            $res = Util::getWordsApi()->convertDocument($req);
            $this->convertedFilePath = $res->getPathname();
        } catch (ApiException $x) {
            $this->errorDetails = $x->getMessage();
        } finally {
            $file = null;
        }
    }

    public function getError()
    {
        if (isset($this->errorDetails)) {
            return $this->errorDetails;
        } else {
            return null;
        }
    }

    /**
     * Delete temporary/generated files
     */
    public function clean()
    {
        @unlink($this->convertedFilePath);
        unset($this->convertedFilePath);
    }

    public function getConvertedFilePath()
    {
        return $this->convertedFilePath;
    }

    public function converted()
    {
        return isset($this->convertedFilePath) && !empty($this->convertedFilePath);
    }

}
