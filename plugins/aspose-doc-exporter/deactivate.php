<?php

defined("ASPOSE_WORDS_PLUGIN_FILE") || die();

register_deactivation_hook(ASPOSE_WORDS_PLUGIN_FILE, function() {
    $options_list = [
        "aspose-cloud-app-sid",
        "aspose-cloud-app-key",
        "aspose-cloud-activation-secret",
        "aspose_doc_exporter_app_sid",
        "aspose_doc_exporter_app_key",
        "aspose_doc_exporter_comments_text",
        "aspose_doc_exporter_post_comments",
        "aspose_doc_exporter_archive_posts",
        "aspose_doc_exporter_file_type",
        "aspose_doc_exporter_post_content_filters",
        "aspose_doc_exporter_post_date",
        "aspose_doc_exporter_post_author",
        "aspose_doc_exporter_excerpt",
        "aspose_doc_exporter_post_categories",
        "aspose_doc_exporter_content",
        "aspose_doc_exporter_metadata",
        "aspose_doc_exporter_enable_background_exports",
        "aspose_doc_exporter_do_shortcode",
    ];

    foreach ($options_list as $option_name) {
        delete_option($option_name);
    }
});
