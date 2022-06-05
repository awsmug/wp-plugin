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
     * @var PluginInfo
     */
    private $info;

    public function __construct()
    {
        if ($this->check()) {
            $this->loadTextDomain();
            $this->load();
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
        $currentWPVersion = get_bloginfo('version');
        $requiredWPVersion = $this->info()->getRequiredWPVersion();

        if (!empty($requiredWPVersion) && version_compare($currentWPVersion, $requiredWPVersion, '<')) {
            throw new PluginException(
                sprintf(
                    'Plugin Requires version %s of WordPress. Version %s given',
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
                    'Plugin Requires version %s of PHP. Version %s given',
                    $requiredPHPVersion,
                    $currentPHPVersion
                )
            );
        }

        return true;
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
        $textDomain = $this->info()->getTextDomain();
        $domainPath = $this->info()->getDomainPath();

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
     * Have to be loaded by register_activation_hook(__FILE__, [$pluginInstance, 'activate']) on main plugin class.
     * 
     * @since 1.0.0
     */
    public function activate()
    {
    }

    /**
     * Deactavation scripts.
     * 
     * Have to be loaded by register_deactivation_hook(__FILE__, [$pluginInstance, 'deactivate']) on main plugin class.
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
    public function info(): PluginInfo
    {
        if (empty($this->info)) {
            $calledClass = get_called_class();
            $reflector   = new \ReflectionClass($calledClass);

            $this->info = new PluginInfo($reflector->getFileName());
        }

        return $this->info;
    }
}
