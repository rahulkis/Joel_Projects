<?php

namespace AsposeWords;

class DownloadColumn {

    public static function register() {
        $i = new DownloadColumn();
        add_action("init", array($i, "enqueue"));
        add_filter("manage_posts_columns", array($i, "define_column"));
        add_filter("manage_pages_columns", array($i, "define_column"));
        add_action("manage_posts_custom_column", array($i, "column_content"), 1, 2);
        add_action("manage_pages_custom_column", array($i, "column_content"), 1, 2);
    }

    public function enqueue() {
        wp_enqueue_script("jquery");
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-dialog");
        wp_enqueue_style("wp-jquery-ui-dialog");
    }

    public function define_column($columns) {
        $columns["aspose_words_exported_file"] = "Export to " . strtoupper(get_option("aspose_doc_exporter_file_type"));
        return $columns;
    }

    public function column_content($column_name, $post_id) {
        if ("aspose_words_exported_file" !== $column_name) {
            return;
        }

        $engine = new ExportEngine($post_id);
        require dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/templates/posts-column-download-button.php";
    }
}
