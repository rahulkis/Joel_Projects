<?php

namespace AsposeWords;

class ActivationNotice
{

    public const DEFAULT_ASPOSE_CLOUD_MARKETPLACE_ACTIVATOR_URL = "https://activator.marketplace.aspose.cloud/";

    public static function register()
    {
        $i = new ActivationNotice();
        add_action("admin_notices", array($i, "activationNotice"));
    }

    public function activationNotice()
    {
        update_option("aspose-cloud-activation-secret", bin2hex(random_bytes(313)));

        $activation_url = null;
        if (array_key_exists("ASPOSE_CLOUD_MARKETPLACE_ACTIVATOR_URL", $_ENV) && trim($_ENV["ASPOSE_CLOUD_MARKETPLACE_ACTIVATOR_URL"]) !== '') {
            $activation_url = trim($_ENV["ASPOSE_CLOUD_MARKETPLACE_ACTIVATOR_URL"], '/');
        } else {
            $activation_url = trim(self::DEFAULT_ASPOSE_CLOUD_MARKETPLACE_ACTIVATOR_URL, '/');
        }

	    $activation_url .= "/activate?callback=" . urlencode(site_url()) . "&secret=" . get_option("aspose-cloud-activation-secret");
        include_once dirname(ASPOSE_WORDS_PLUGIN_FILE) . "/templates/activation-notice.php";
    }
}
