# Awesome WP Plugin

This is a little helper which lets you get start faster with a WordPress Plugin.

## Installing


Get the helper into your plugin with composer.

```composer require awsm/wp-plugin```


## Using in code (Example Plugin)

This is an example of a plugin created with the WP_Plugin parent class. Looks like nothing, but in the background checks are  made with the plugin header information and dont't have to be done anymore.

At the moment the following tests are made and an error message will be shown in the admin if requirements are not fullfilled:

- Minimum PHP requirement
- Minimum WordPress requirement

Also the Textdomain will be loaded and will occur an error in the backend if it could not be loaded.

There will be no checks if there are no parameters for that in the plugin file.

```php
<?php

/**
 * Plugin Name: Example Plugin
 * Description: Test Plugin for WP Plugin class.
 * Requires PHP: 7.0.0
 * Requires at least: 6.0.0
 * Domain Path: /langauges
 * Text Domain: wp-plugin
 */
namespace AWSM\WP_Plugin;

require dirname( __DIR__ ) . '/vendor/autoload.php';

use AWSM\WP_Plugin\Plugin;

class ExamplePlugin extends Plugin {
    /**
     * load() is the method which will be executed after 
     * all checks have been passed and Text domain was loaded.
     * 
     * The method is mandatory.
     */
    public function load()
    {
        add_action('admin_notices', [$this, 'show_notice']);
    }

    /**
     * Just an example.
     */
    public function show_notice() 
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
        </div>
        <?php
    }

    public function activate() {
        // Activation code here
    }

    public function deactivate() {
        // Activation code here
    }
}