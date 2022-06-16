<?php

namespace AWSM\WP_Plugin;

/**
 * Represents a plugin.
 * 
 * @since 1.0.0
 */
abstract class Plugin
{
    /**
     * Plugin information.
     * 
     * @var PluginInfo Plugin information object.
     */
    private static $info;

    /**
     * Plugin filename.
     * 
     * @var string Plugin filename.
     */
    private $pluginFilename;

    /**
     * Error message.
     * 
     * @var string Message text.
     */
    private $errorMessage;

    public function __construct()
    {
        register_activation_hook($this->getPluginFilename(), [$this, 'activate']);
        register_deactivation_hook($this->getPluginFilename(), [$this, 'deactivate']);

        try {
            if ($this->check()) {
                $this->loadTextDomain();
                $this->load();
            }
        } catch (PluginException $e) {
            $this->errorMessage = $e->getMessage();
            add_action('admin_notices', [$this, 'showErrorMessage']);
        }
    }

    /**
     * Abstract function for loading plugin stuff.
     * 
     * @since 1.0.0
     */
    abstract public function load();

    /**
     * Check for requirements.
     * 
     * @return bool True if check passed.
     * 
     * @throws PluginException Error message.
     * 
     * @since 1.0.0
     */
    protected function check(): bool
    {
        $pluginName = $this->info()->getName();

        $currentWPVersion = get_bloginfo('version');
        $requiredWPVersion = $this->info()->getRequiredWPVersion();

        if (!empty($requiredWPVersion) && version_compare($currentWPVersion, $requiredWPVersion, '<')) {
            throw new PluginException(
                sprintf(
                    'Plugin "%s" stopped. It requires version %s of WordPress. Version %s given. Please update WordPress.',
                    $pluginName,
                    $requiredWPVersion,
                    $currentWPVersion
                )
            );
        }

        $currentPHPVersion = PHP_VERSION;
        $requiredPHPVersion = $this->info()->getRequiredPHPVersion();

        if (!empty($requiredPHPVersion) && version_compare($currentPHPVersion, $requiredPHPVersion, '<')) {
            throw new PluginException(
                sprintf(
                    'Plugin "%s" stopped. It requires version %s of PHP. Version %s given. Please contact your hoster or go to your hosting admin panel to update to a newer PHP version.',
                    $pluginName,
                    $requiredPHPVersion,
                    $currentPHPVersion
                )
            );
        }

        return true;
    }

    /**
     * Show error message.
     * 
     * @since 1.0.0
     */
    public function showErrorMessage() {
        ?>
        <div class="error is-dismissible">
            <p><?php echo $this->errorMessage; ?></p>
        </div>
        <?php
    }

    /**
     * Loading text domain.
     * 
     * @throws PluginException Error message.
     * 
     * @since 1.0.0
     */
    private function loadTextDomain()
    {
        $textDomain = self::info()::getTextDomain();
        $domainPath = self::info()::getDomainPath();

        if (empty($textDomain) || empty($domainPath)) {
            return;
        }

        if (!load_plugin_textdomain($textDomain, false, $domainPath)) {
            throw new PluginException(
                sprintf(
                    'Textdomain %s file not found in %s.',
                    $textDomain,
                    $domainPath
                )
            );
        }
    }

    /**
     * Actavation scripts.
     * 
     * @since 1.0.0
     */
    public function activate()
    {
    }

    /**
     * Deactavation scripts.
     * 
     * @since 1.0.0
     */
    public function deactivate()
    {
    }

    /**
     * Plugin information object.
     * 
     * Loads informations from the initiaded class which have to be startet in file with plugin headerts.
     * 
     * @return PluginInfo PluginInfo object.
     * 
     * @since 1.0.0
     */
    public static function info(): PluginInfo
    {
        if (empty(self::$info)) {
            self::$info = new PluginInfo(self::getPluginFilename());
        }

        return self::$info;
    }

    /**
     * Get the filename of the called plugin class.
     * 
     * @return string Plugin filename.
     * 
     * @since 1.0.0
     */
    private static function getPluginFilename(): string
    {
        if (empty(self::$pluginFilename)) {
            $calledClass = get_called_class();
            $reflector   = new \ReflectionClass($calledClass);
            self::$pluginFilename = $reflector->getFileName();
        }

        return self::$pluginFilename;
    }
}
