(function ($) {
    $(document).ready(function () {

        $("#aspose_words_import_meta_box_in_progress").hide();
        $("#aspose_words_import_meta_box_popup").show();

        $(document).on("click", "#aspose_words_import_meta_box_popup", function (e) {
            e.preventDefault();

            frame = wp.media({
                title: "Select a Word file to import",
                button: {
                    text: "Import to Current Post"
                },
                multiple: false
            });

            frame.on("select", function () {
                $("#aspose_words_import_meta_box_popup").hide();
                $("#aspose_words_import_meta_box_in_progress").show();

                var attachment = frame.state().get("selection").first().toJSON();
                var data = {
                    "action": "aspose_doc_to_html",
                    "post_id": attachment.id
                };

                $.post(ajaxurl, data)
                    .done(function (text) {

                        if (typeof wp.blocks !== 'undefined') {
                            b = wp.blocks.createBlock("core/freeform", {
                                content: text,
                            });
                            wp.data.dispatch("core/block-editor").insertBlock(b);
                        } else {
                            send_to_editor(text);
                        }

                        $("#aspose_words_import_meta_box_in_progress").hide();
                        $("#aspose_words_import_meta_box_popup").show();
                    })
                    .fail(function (details) {

                        console.error(details);
                        alert("An error occurred. Please try again.");

                        $("#aspose_words_import_meta_box_in_progress").hide();
                        $("#aspose_words_import_meta_box_popup").show();

                    })
                ;
            });

            frame.open();
        })
    });
})(jQuery);


