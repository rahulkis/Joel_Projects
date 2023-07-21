<?php

/**
 * Plugin Name:         Aspose.Words
 * Plugin URI:          https://www.aspose.cloud/
 * Version:		6.3.1
 * Description:         Export WordPress posts and pages as DOCX, DOC, ODT Word documents
 * Requires at least:   5
 * Requires PHP:        7.2.5
 * Author:              aspose.cloud Marketplace
 * Author URI:          https://www.aspose.cloud/
 * License:             GPLv2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 */
define("ASPOSE_WORDS_PLUGIN_FILE", __FILE__);

@include_once(dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/local.php");
require_once __DIR__ . "/vendor/autoload.php";


try {
    \Dotenv\Dotenv::createImmutable(__DIR__)->load();
} catch (\Dotenv\Exception\InvalidPathException $x) {
    // Ignore
}

if (strlen(get_option("aspose-cloud-app-sid")) < 1) {
    \AsposeWords\ActivationNotice::register();
    \AsposeWords\Activation::register();
} else {
    \AsposeWords\AdminSettingsUI::register();
    \AsposeWords\BulkExportUI::register();
    \AsposeWords\Doc2HtmlAjax::register();
    \AsposeWords\ImportMetaBox::register();
    if (get_option("aspose_doc_exporter_enable_background_exports") === "1") {
        \AsposeWords\AutoExport::register();
        \AsposeWords\DownloadColumn::register();
        \AsposeWords\ExportWidget::register();
    }
}

