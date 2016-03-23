<?php

define('XT_DOMAIN', 'xt_plugin');
define('XT_FS_PATH', trailingslashit(__DIR__));
define('XT_WS_PATH', plugin_dir_url(__FILE__));
define('XT_CRON_URL', XT_WS_PATH . 'cron.php');
define('XT_PLUGIN_ACTIVE', true);