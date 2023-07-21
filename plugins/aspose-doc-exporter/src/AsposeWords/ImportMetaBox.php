<?php

namespace AsposeWords;

class ImportMetaBox {

	public static function register() {
		$i = new ImportMetaBox();
		//add_action( "media_buttons_context", array( $i, "media_buttons" ) );
		add_action("init", array($i, "register_script"));
		add_action("add_meta_boxes", array($i, "add_meta_boxes"));
		add_action("admin_enqueue_scripts", array($i, "admin_enqueue_scripts"));

	}

	function register_script() {
		wp_register_script(
			"aspose_words_import_meta_box_script",
			plugins_url("src/AsposeWords/ImportMetaBox.js", ASPOSE_WORDS_PLUGIN_FILE),
			array(
				"jquery"
			),
            Util::getPluginData("Version")
		);
	}

	function admin_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script("aspose_words_import_meta_box_script");
	}

	function add_meta_boxes() {
		add_meta_box(
			"aspose_words_import_meta_box",
            Util::getPluginData("Name"),
			array($this, "render_meta_box_1"),
			array("post", "page"),
			"side",
			"high"
		);
	}

	function render_meta_box_1($context) {
        include_once dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/templates/import-metabox.php";
	}
}
