<div class="notice notice-error">
    <h3>
        You are almost ready to start using
        <?php echo get_plugin_data(ASPOSE_WORDS_PLUGIN_FILE)["Name"]; ?>
    </h3>
    <p>
        <?php echo get_plugin_data(ASPOSE_WORDS_PLUGIN_FILE)["Name"]; ?>
        requires an active subscription at
        <a href="https://www.aspose.cloud/"><b>aspose.cloud</b></a>
        which is completely FREE for our WordPress users. No Sign Up needed.
    </p>
    <h1>
        <a class="button button-primary button-hero" href="<?php echo $activation_url; ?>">
            <b>Enable FREE and Unlimited Access</b>
        </a>
    </h1>
    <p style="font-size: xx-small">
        Your website URL <i><?php echo site_url(); ?></i>
        and admin email <i><?php echo get_bloginfo("admin_email"); ?></i>
        will be sent to <i>aspose.cloud</i>.
        Check our
        <a href="https://company.aspose.cloud/legal/privacy-policy">privacy policy</a>.
    </p>
</div>
