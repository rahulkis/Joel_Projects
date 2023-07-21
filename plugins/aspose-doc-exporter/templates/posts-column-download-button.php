<?php if (file_exists($engine->getPath())): ?>

    <a href="<?php echo $engine->getExportedFileUrl(); ?>">
        <span class="dashicons dashicons-download"></span>
    </a>

<?php else: ?>

    <a href="#" id="aspose-words-posts-download-instruction-button-for-post-<?php echo $post_id?>">
        <span class="dashicons dashicons-info"></span>
    </a>

<?php endif; ?>

<div id="aspose-words-posts-download-instruction-dialog-for-post-<?php echo $post_id?>" class="hidden" style="max-width:700px">
    <h2 autofocus="autofocus">The post you are trying to download is not exported yet.</h2>
    <h4>You have the following options:</h4>
    <ol>
        <li>
            Click <b><code>View Post</code></b> to let the post be exported in background and than <b><code>Back to List</code></b> to download it.
        </li>
        <li>
            Click <b><code>Back to List</code></b> and use the <b>Export to <?php echo strtoupper(get_option("aspose_doc_exporter_file_type")); ?></b> option from <b>Bulk actions</b> and click <b>Apply</b>.
        </li>
    </ol>
</div>

<script>
(function($) {
    $(document).on("click", "#aspose-words-posts-download-instruction-button-for-post-<?php echo $post_id?>", function(e) {
        e.preventDefault();
        $('#aspose-words-posts-download-instruction-dialog-for-post-<?php echo $post_id?>').dialog({
            title: "Aspose.Words: Where is the exported file?",
            dialogClass: 'wp-dialog',
            autoOpen: true,
            draggable: false,
            width: 'auto',
            modal: true,
            resizable: false,
            closeOnEscape: true,
            position: {
                my: "center",
                at: "center",
                of: window
            },
            buttons: [
                {
                    text: "View Post",
                    icon: "ui-icon-extlink",
                    click: function(e) {
                        window.open("<?php echo get_permalink($post_id); ?>", "_blank");
                    }
                },
                {
                    text: "Back to list",
                    icon: "ui-icon-refresh",
                    click: function(e) {
                        window.location.reload(true);
                    }
                }

            ]
        });
    });

    $(document).on('click', ".ui-widget-overlay", function(){
        $('#aspose-words-posts-download-instruction-dialog-for-post-<?php echo $post_id?>').dialog("destroy");
    });

})(jQuery);
</script>
