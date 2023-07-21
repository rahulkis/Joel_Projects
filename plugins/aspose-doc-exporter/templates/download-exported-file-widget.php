<?php
/*
 * @see \AsposeWords\ExportWidget
 */
?>

<a href="<?php echo $engine->getExportedFileUrl(); ?>">
    Download post as <?php echo strtoupper(get_option("aspose_doc_exporter_file_type")); ?>
</a>
