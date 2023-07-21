<?php

namespace AsposeWords;

class AutoExport {

    public static function register() {
        $i = new AutoExport();
        add_action("shutdown", array($i, "export"));
        add_action("save_post", array($i, "delete_outdated_file"));
    }

    public function export() {
        if (!is_singular() || is_admin()) {
            return;
        }

        $e = new ExportEngine(get_the_ID());

        if (file_exists($e->getPath())) {
            return;
        }

        try {
            $e->convertToHtml();
            $e->convert();
            $e->copyConvertedFileTo($e->getPath());
            $e->autoclean();
        } catch (GuzzleHttp\Exception\ClientException $x) {
            $err = json_decode($x->getResponse()->getBody(true));
            wp_die($err);
        } catch (GuzzleHttp\Exception\ServerException $x) {
            $err = json_decode($x->getResponse()->getBody(true));
            wp_die($err);
        } catch (Exception $x) {
            wp_die($x);
        }

    }

    public function delete_outdated_file($post_id) {
        $e = new ExportEngine($post_id);
        if (file_exists($e->getPath())) {
            unlink($e->getPath());
        }
    }

}
