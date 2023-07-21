<?php

namespace AsposeWords;

class BulkExportUI {

    public static function register() {
        $i = new BulkExportUI();
        global $wpdb;
        $post_types = $wpdb->get_col("SELECT DISTINCT(post_type) FROM $wpdb->posts");
        foreach ($post_types as $t) {
            add_filter("bulk_actions-edit-$t", array($i, "bulkActionMenu"));
            add_filter("handle_bulk_actions-edit-$t", array($i, "exportBulkAction"), 10, 3);
        }
    }

    public function bulkActionMenu($bulk_actions) {
        $bulk_actions["AsposeDocExporter_export"] = __(
                "Export to " . strtoupper(get_option("aspose_doc_exporter_file_type")) . " (" . get_plugin_data(ASPOSE_WORDS_PLUGIN_FILE)["Name"] . ")",
                "AsposeDocExporter_export"
        );
        return $bulk_actions;
    }

    public function exportBulkAction($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'AsposeDocExporter_export' || count($post_ids) < 1) {
            return $redirect_to;
        }

        if (count($post_ids) === 1 || get_option("aspose_doc_exporter_archive_posts") !== "1") {
            $e = new ExportEngine($post_ids);
            try {
                $e->convertToHtml();
                $e->convert();
                header("Content-type: application/octet-stream");
                header("Content-disposition: attachment; filename=\"" . $e->getSlug() . "." . strtolower(get_option("aspose_doc_exporter_file_type")) . "\";");
                readfile($e->getConvertedFilePath());
                $e->clean();
            } catch (GuzzleHttp\Exception\ClientException $x) {
                $err = json_decode($x->getResponse()->getBody(true));
                if ($err->error === "invalid_client") {
                    wp_die("Aspose DOC Exporter: Invalid <b>App SID</b> or <b>App Key</b>. Check your plugin settings and try again.");
                } else {
                    wp_die($err);
                }
            } catch (GuzzleHttp\Exception\ServerException $x) {
                $err = json_decode($x->getResponse()->getBody(true));
                wp_die($err);
            } catch (Exception $x) {
                wp_die($x);
            }
        } else {
            $ee = array();

            foreach ($post_ids as $post_id) {
                try {
                    $ee[] = new ExportEngine($post_id);
                } catch (Exception $x) {
                    wp_die($x);
                }
            }

            foreach ($ee as $e) {
                try {
                    $e->convertToHtml();
                    $e->convert();
                } catch (Exception $x) {
                    wp_die($x);
                }
            }

            $zfile = wp_upload_dir()["path"] . "/exported-posts-" . time() . ".zip";
            $zip = new \ZipArchive();
            if ($zip->open($zfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                foreach ($ee as $e) {
                    $filename = $e->getSlug();
                    $filename = $filename . "." . strtolower(get_option("aspose_doc_exporter_file_type"));
                    $zip->addFile($e->getConvertedFilePath(), $filename);
                }
                $zip->close();
            } else {
                wp_die("Failed to create archive");
            }

            header("Content-type: application/zip");
            header("Content-disposition: attachment; filename=exported-posts" . time() . ".zip;");
            readfile($zfile);

            foreach ($ee as $e) {
                try {
                    $e->clean();
                } catch (Exception $x) {
                }
            }
        }
    }

}
