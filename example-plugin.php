<?php
/**
 * Plugin Name: Example Plugin
 * Description: Test Plugin for WP Plugin class.
 * Requires PHP: 7.0.0
 * Requires at least: 6.0
 * Domain Path: /langauges
 * Text Domain: wp-plugin
 */

require dirname(__FILE__) . '/src/Plugin.php';
require dirname(__FILE__) . '/src/PluginInfo.php';
require dirname(__FILE__) . '/src/PluginException.php';

use AWSM\WP_Plugin\Plugin;

class ExamplePlugin extends Plugin {
    public function load()
    {
        add_action('admin_notices', [$this, 'show_notice']);
    }

    public function show_notice() 
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
        </div>
        <?php
    }
}