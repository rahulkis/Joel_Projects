<?php

namespace AsposeWords;

use Aspose\Words\WordsApi;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class Util
{

    private static $twig;
    private static $wordsApi;

    public static function getWordsApi()
    {
        if (!isset(self::$wordsApi)) {

            if (strlen(get_option("aspose-cloud-app-sid")) < 1) {
                throw new \RuntimeException("Cannot instantiate WordsApi without App SID and Key");
            }

            self::$wordsApi = new WordsApi(
                get_option("aspose-cloud-app-sid"),
                get_option("aspose-cloud-app-key"),
                "https://api.aspose.cloud/"
            );
            //self::$wordsApi->getConfig()->setDebug(WP_DEBUG);
            global $wp_version;
            self::$wordsApi->getConfig()->setUserAgent(sprintf("%s/%s %s/%s WordPress/$wp_version PHP/%s",
                Util::getPluginData("Name"),
                Util::getPluginData("Version"),
                self::$wordsApi->getConfig()->getUserAgent(),
                self::$wordsApi->getConfig()->getClientVersion(),
                PHP_VERSION
            ));
        }
        return self::$wordsApi;
    }

    public static function getTwig()
    {
        if (!isset(self::$twig)) {
            $loader = new FilesystemLoader(dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/templates");
            self::$twig = new Environment($loader, [
                "debug" => defined("WP_DEBUG") && WP_DEBUG
            ]);

            if (self::$twig->isDebug()) {
                self::$twig->addExtension(new DebugExtension());
            }
        }

        return self::$twig;
    }

    public static function fetchPostData($post_id)
    {
        $post = get_post($post_id);
        $post_content = $post->post_content;
        if (get_option("aspose_doc_exporter_do_shortcode")) {
            $post_content = do_shortcode($post_content);
        }
        $postmeta_keys = get_post_custom_keys($post_id);
        if ($postmeta_keys) {
            $postmeta_keys = array_filter(get_post_custom_keys($post_id), function ($item) {
                return trim($item, '_') === $item;
            });
        } else {
            $postmeta_keys = [];
        }
        $author = get_userdata($post->post_author);
        $categories = get_the_terms($post_id, "category");
        $comments = get_comments(["post_id" => $post_id, "status" => "approve"]);

        return [
            "post" => $post,
            "post_content" => $post_content,
            "postmeta_keys" => $postmeta_keys,
            "author" => $author,
            "categories" => $categories,
            "comments" => $comments,
        ];
    }

    public static function getPluginData($field) {
        return get_file_data(
            ASPOSE_WORDS_PLUGIN_FILE,
            array(
                "Name" => "Plugin Name",
                "Version" => "Version",
                ),
            "plugin")
            [$field];
    }
}
