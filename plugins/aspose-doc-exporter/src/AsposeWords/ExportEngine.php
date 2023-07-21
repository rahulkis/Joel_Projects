<?php

namespace AsposeWords;

use Aspose\Words\Model\Requests\ConvertDocumentRequest;

class ExportEngine {

    public function __construct($post_id) {
        if (is_int($post_id)) {
            $this->post_id = $post_id;
        } else if (is_array($post_id) && count($post_id) === 1 && is_int($post_id[0])) {
            $this->post_id = $post_id[0];
        } else if (is_array($post_id)) {
            $this->post_id = $post_id;
        } else if ($post_id instanceof \WP_Post) {
            $this->post_id = $post_id->ID;
        } else {
            throw new \RuntimeException("Cannot instantiate without a Post ID");
        }
    }

    public function getSlug() {
        if (is_array($this->post_id)) {
            return "exported-posts-" . time();
        } else {
            return get_post($this->post_id)->post_name;
        }
    }

    public function getFileExtention() {
        $e = get_option("aspose_doc_exporter_file_type");
        if (strlen($e) < 1) {
            return "docx";
        } else {
            return $e;
        }
    }

    public function getFilename() {
        return $this->getSlug() . "." . $this->getFileExtention();
    }

    public function getPath() {
        $dir = wp_get_upload_dir()["basedir"] . "/Aspose.Words";
        wp_mkdir_p($dir);
        return $dir . "/" . $this->getFilename();
    }

    public function getExportedFileUrl() {
        return wp_get_upload_dir()["baseurl"] . "/Aspose.Words/" . $this->getFilename();
    }

    public function getHtml() {
        if (is_array($this->post_id)) {
            $list = [];
            foreach ($this->post_id as $i) {
                $list[$i] = Util::fetchPostData($i);
            }
            return Util::getTwig()->render("posts.twig", [
                        "list" => $list,
                        "options" => wp_load_alloptions(),
            ]);
        } else {
            return Util::getTwig()->render("post.twig", array_merge(
                                    Util::fetchPostData($this->post_id),
                                    [
                                        "options" => wp_load_alloptions(),
                                    ]
            ));
        }
    }

    public function convertToHtml() {
        $this->htmlFilePath = get_temp_dir() . "/" . $this->getSlug() . ".html";
        file_put_contents(
                $this->htmlFilePath,
                $this->getHtml()
        );
    }

    public function convert() {
        $file = null;
        try {
            $file = new \SplFileObject($this->getHtmlFilePath());
            $req = new ConvertDocumentRequest($file, strtoupper($this->getFileExtention()), null);
            $res = Util::getWordsApi()->convertDocument($req);
            $this->convertedFilePath = $res->getPathname();
        } catch (Exception $x) {
            throw $x;
        } finally {
            $file = null;
        }
    }

    /**
     * Delete temporary/generated files
     */
    public function clean() {
        @unlink($this->htmlFilePath);
        unset($this->htmlFilePath);
        @unlink($this->convertedFilePath);
        unset($this->convertedFilePath);
    }

    public function autoclean() {
        if (!isset($this->autoclean_registered) && $this->autoclean_registered === true) {
            $this->autoclean_registered = register_shutdown_function(array($this, "clean"));
        }
    }

    private function getHtmlFilePath() {
        return $this->htmlFilePath;
    }

    public function getConvertedFilePath() {
        return $this->convertedFilePath;
    }

    public function copyConvertedFileTo($dest) {
        copy($this->convertedFilePath, $dest);
    }

    public function exported() {
        return isset($this->htmlFilePath) && !empty($this->htmlFilePath);
    }

    public function converted() {
        return isset($this->convertedFilePath) && !empty($this->convertedFilePath);
    }

    private $autoclean_registered;
}
