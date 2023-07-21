<?php


namespace AsposeWords;


class Doc2HtmlAjax
{
    public static function register()
    {
        $i = new Doc2HtmlAjax();
        add_action("wp_ajax_aspose_doc_to_html", array($i, "callback"));
    }

    public function callback()
    {
        $post = get_post($_POST["post_id"]);
        $path_to_file = get_attached_file($post->ID);
        $e = new ImportEngine($path_to_file);
        $e->convert();
        if ($e->converted()) {
            echo $e->getHtml();
        } else {
            echo $e->getError();
        }
        exit();
    }
}
