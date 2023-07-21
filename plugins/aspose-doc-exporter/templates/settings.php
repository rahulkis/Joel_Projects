<div class="wrap">
    <h1>
        Aspose.Words Settings
    </h1>

    <div class="metabox-holder has-right-sidebar">
        <div class="inner-sidebar" id="side-info-column">
            <div class="meta-box-sortables ui-sortable" id="side-sortables">

                <div class="postbox">
                    <h3 class="hndle">Help and Support</h3>
                    <div class="inside">
                        For any suggestion, query, issue and requirement
                        please feel free to post on
                        <a href="https://forum.aspose.cloud/c/words">
                            Aspose.Words Support Forum
                        </a>.
                    </div>
                </div>

                <div class="postbox">
                    <h3 class="hndle">App SID</h3>
                    <div class="inside">
                        While talking to our support staff,
                        you may be asked for your App SID.
                        Use the following code.
                        <br/>
                        <code style="font-size: xx-small">
                            <?php echo get_option("aspose-cloud-app-sid"); ?>
                        </code>
                    </div>
                </div>

                <div class="postbox">
                    <h3 class="hndle">Feedback and Review</h3>
                    <div class="inside">
                        Please feel free to add your reviews on
                        <a href="https://wordpress.org/support/plugin/aspose-doc-exporter/reviews/" target="_blank">
                            WordPress Plugin Directory
                        </a>.
                    </div>
                </div>
            </div>
        </div>

        <div id="post-body">
            <div id="post-body-content">

                <div class="postbox">
                    <h3 class="hndle">aspose.cloud Subscription</h3>
                    <div class="inside">
                        Your FREE and Unlimited Access is enabled.
                        You can use
                        <b>Aspose.Words</b>
                        to import and export posts and pages.
                    </div>
                </div>

                <div class="postbox">
                    <div class="inside">
                        <form method="post" action="options.php">
                            <?php settings_fields("aspose_doc_exporter_options"); ?>
                            <?php do_settings_sections(dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE)));?>
                            <?php submit_button(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
