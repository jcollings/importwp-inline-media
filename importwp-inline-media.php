<?php

/**
 * Plugin Name: Import WP - Import Inline Media
 * Plugin URI: https://www.importwp.com
 * Description: Import Media from imported post content into the wordpress media library
 * Author: James Collings <james@jclabs.co.uk>
 * Version: 1.0.0 
 * Author URI: https://www.importwp.com
 * Network: True
 */

if (!defined('IWP_INLINE_MEDIA_MIN_CORE_VERSION')) {
    define('IWP_INLINE_MEDIA_MIN_CORE_VERSION', '2.14.1');
}

add_action('admin_init', 'iwp_inline_media_check');

function iwp_inline_media_requirements_met()
{

    if (!current_user_can('activate_plugins')) {
        return false;
    }

    if (!function_exists('import_wp')) {
        return false;
    }

    if (version_compare(IWP_VERSION, IWP_INLINE_MEDIA_MIN_CORE_VERSION, '<')) {
        return false;
    }

    return true;
}

function iwp_inline_media_check()
{
    if (!iwp_inline_media_requirements_met()) {

        add_action('admin_notices', 'iwp_inline_media_notice');

        deactivate_plugins(plugin_basename(__FILE__));

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

function iwp_inline_media_setup()
{
    if (!iwp_inline_media_requirements_met()) {
        return;
    }

    $base_path = dirname(__FILE__);

    // require_once $base_path . '/class/autoload.php';
    require_once $base_path . '/setup.php';

    // Install updater
    if (file_exists($base_path . '/updater.php') && !class_exists('IWP_Updater')) {
        require_once $base_path . '/updater.php';
    }

    if (class_exists('IWP_Updater')) {
        $updater = new IWP_Updater(__FILE__, 'importwp-table');
        $updater->initialize();
    }
}
add_action('plugins_loaded', 'iwp_inline_media_setup', 9);

function iwp_inline_media_notice()
{
    echo '<div class="error">';
    echo '<p><strong>Import WP - Import Inline Media</strong> requires that you have <strong>Import WP v' . IWP_INLINE_MEDIA_MIN_CORE_VERSION . ' or newer</strong> installed.</p>';
    echo '</div>';
}
