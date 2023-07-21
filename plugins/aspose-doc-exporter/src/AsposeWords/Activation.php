<?php

namespace AsposeWords;

use Firebase\JWT\JWT;

class Activation {

    public static function register() {
        if (array_key_exists("token", $_REQUEST) && get_option("aspose-cloud-activation-secret")) {
            $i = new Activation();
            add_action("init", array($i, "callback"));
        }
    }

    public function callback()
    {
        if (null === ($token = $this->getToken())) {
            return;
        }

        update_option("aspose-cloud-app-key", $token["aspose-cloud-app-key"]);
        update_option("aspose-cloud-app-sid", $token["aspose-cloud-app-sid"]);
        update_option("aspose-cloud-activation-secret", null);

        if (wp_redirect(admin_url("options-general.php?page=" . dirname(plugin_basename(ASPOSE_WORDS_PLUGIN_FILE))))) {
            exit;
        }
    }

    public function getToken() {
        try {
            $data = (array)JWT::decode($_REQUEST["token"], get_option("aspose-cloud-activation-secret"), array("HS256"));
            if (!isset($data["iss"]) || "https://activator.marketplace.aspose.cloud/" !== $data["iss"]) {
                // Skip the token silently, as it may not be a JWT token or may be issued by someone else.
                return;
            }
            return $data;
        } catch (Exception $x) {
            return null;
        }
    }
}
