<?php

namespace AsposeWords;

class AdminSettingsUI
{

    public static function register()
    {
        $i = new AdminSettingsUI();
        add_action("admin_init", array($i, "registerAdminSettings"));
        add_action('admin_menu', array($i, "adminMenu"));

        add_filter(
            "plugin_action_links_" . plugin_basename(ASPOSE_WORDS_PLUGIN_FILE),
            array($i, "pluginActionLinks")
        );
    }

    public function adminMenu()
    {
        add_options_page(
            get_plugin_data(ASPOSE_WORDS_PLUGIN_FILE)["Name"] . " " . __("Settings"),
            get_plugin_data(ASPOSE_WORDS_PLUGIN_FILE)["Name"],
            "manage_options",
            dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE)),
            array($this, "settingsPage")
        );
    }

    public function pluginActionLinks($default_links)
    {
        $links = array(
            sprintf("<a href=\"%s\">%s</a>",
                admin_url("options-general.php?page=" . dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE))),
                __("Settings")
            )
        );
        return array_merge($links, $default_links);
    }

    public function registerAdminSettings()
    {
        add_settings_section(
            "aspose_words_settings_exported_content",
            "Content of exported document",
            function () {
                echo "Select what parts of the post you want to include in the exported document.";
            },
            dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE))
        );
        add_settings_section(
            "aspose_words_settings_advanced_export_options",
            "Advanced export settings",
            function () {
                echo "Advanced settings that impact exported document and its behaviour.";
            },
            dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE))
        );

        $this->registerBooleanSettings("aspose_words_settings_exported_content", [
            [
                "aspose_doc_exporter_post_date",
                "Post date",
                "The date of publishing of post.",
                null
            ],
            [
                "aspose_doc_exporter_post_author",
                "Post author",
                "The user who wrote the post.",
                null
            ],
            [
                "aspose_doc_exporter_excerpt",
                "Excerpt",
                "Short summary of post. Mostly, it is the first paragraph of post.",
                null
            ],
            [
                "aspose_doc_exporter_content",
                "Body",
                "You can also skip the post content altogether. Post content means the post body only.",
                null
            ],
            [
                "aspose_doc_exporter_post_categories",
                "Categories",
                "List of categories the post was added to.",
                null
            ],
            [
                "aspose_doc_exporter_metadata",
                "Custom fields",
                "Any custom fields or additional metadata attached to post.",
                null
            ],
            [
                "aspose_doc_exporter_post_comments",
                "Comments",
                "Include post comments in exported document.",
                null
            ],
        ]);

        register_setting(
            "aspose_doc_exporter_options",
            "aspose_doc_exporter_comments_text",
            [
                "type" => "string"
            ]
        );
        add_settings_field(
            "aspose_doc_exporter_comments_text",
            "Heading for comments section",
            function ($args) {
                ?>
                <label>
                    <input name="aspose_doc_exporter_comments_text" id="aspose_doc_exporter_comments_text" type="text"
                           value="<?php echo get_option("aspose_doc_exporter_comments_text"); ?>"
                    />
                </label>
                <p style="font-size: smaller">Defaults to <i>Comments</i></p>
                <?php
            },
            dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE)),
            "aspose_words_settings_exported_content"
        );

        $this->registerBooleanSettings("aspose_words_settings_advanced_export_options", [
            [
                "aspose_doc_exporter_enable_background_exports",
                "Background exports",
                "Automatically export post in background as post are viewed by users.",
                "This method keeps maximum compatibility with third-party plugins and themes."
                . " In order to allow visitors to download posts as document files, you need to keep this options enabled.",
            ],
            [
                "aspose_doc_exporter_do_shortcode",
                "Shortcodes",
                "Pre-process shortcodes before exporting document.",
                null
            ],
            [
                "aspose_doc_exporter_archive_posts",
                "Multiple posts as archive",
                "Keep each post in a separate document and put them all in ZIP archive for downloading.",
                "If this option is disabled, multiple posts will be combined into a single document during export."
            ],
        ]);

        register_setting(
            "aspose_doc_exporter_options",
            "aspose_doc_exporter_file_type",
            [
                "type" => "string",
                "default" => "docx"
            ]
        );
        add_settings_field(
            "aspose_doc_exporter_file_type",
            "File type of exported document",
            function ($args) {
                ?>
                <label>
                    <select name="aspose_doc_exporter_file_type" id="aspose_doc_exporter_file_type">
                        <?php foreach ($args["option_values"] as $value => $caption): ?>
                            <option value="<?php echo $value; ?>" <?php echo ($value === get_option("aspose_doc_exporter_file_type"))?"selected='selected'":""; ?>><?php echo $caption; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php
            },
            dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE)),
            "aspose_words_settings_advanced_export_options",
            [
                "option_values"=> [
                    "docx" => "Microsoft Word Document (DOCX)",
                    "doc" => "Microsoft Word Document 97-2003 (DOC)",
                    "odt" => "OpenDocument Text (ODT)",
                    "rtf" => "Rich Text Format (RTF)",
                    "txt" => "Unformatted text"
                ],
            ]
        );
    }

    public function settingsPage()
    {
        include_once dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/templates/settings.php";
    }

    public function registerBooleanSettings($settings_section, $option_list) {
        foreach ($option_list as $opt) {
            $option_name = $opt[0];
            $option_title=$opt[1];
            $option_description = $opt[2];
            $option_details = $opt[3];

            register_setting(
                "aspose_doc_exporter_options",
                $option_name,
                [
                    "type" => "boolean"
                ]
            );
            add_settings_field(
                $option_name,
                $option_title,
                function ($args) {
                    ?>
                    <label>
                        <input type="checkbox"
                               name="<?php echo $args["option_name"]; ?>"
                               id="<?php echo $args["option_name"]; ?>"
                            <?php checked(true, (in_array(get_option($args["option_name"]), array(false, '1'), true))); ?>
                               value="1">
                        <?php echo $args["option_description"]; ?>
                    </label>
                    <p style="font-size: smaller"><?php echo $args["option_details"]; ?></p>
                    <?php
                },
                dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE)),
                $settings_section,
                [
                    "option_name" => $option_name,
                    "option_description"=> $option_description,
                    "option_details" => $option_details,
                ]
            );
        }

    }
}
