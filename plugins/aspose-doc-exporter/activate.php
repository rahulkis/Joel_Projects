<?php

defined("ASPOSE_WORDS_PLUGIN_FILE") || die();

register_activation_hook(ASPOSE_WORDS_PLUGIN_FILE, function() {
    $default_true_options_list = [
        "aspose_doc_exporter_post_comments",
        "aspose_doc_exporter_archive_posts",
        "aspose_doc_exporter_post_date",
        "aspose_doc_exporter_post_author",
        "aspose_doc_exporter_excerpt",
        "aspose_doc_exporter_post_categories",
        "aspose_doc_exporter_content",
        "aspose_doc_exporter_metadata",
        "aspose_doc_exporter_enable_background_exports",
        "aspose_doc_exporter_do_shortcode",
    ];
    foreach ($default_true_options_list as $option_name) {
        update_option($option_name, "1");
    }
    update_option("aspose_doc_exporter_file_type", "docx");
});
