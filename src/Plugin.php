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
        if (version_compare(get_bloginfo('version'), $this->info()->getRequiredWPVersion(), '<')) {
            throw new PluginException(sprintf('Plugin Requires version %s of WordPress. Version %s given', $this->info()->getRequiredWPVersion(), get_bloginfo('version')));
        }

        if (version_compare(PHP_VERSION, $this->info()->getRequiredPHPVersion(), '<')) {
            throw new PluginException(sprintf('Plugin Requires version %s of PHP. Version %s given', $this->info()->getRequiredPHPVersion(), PHP_VERSION));
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

        if (!load_plugin_textdomain($textDomain, false, $domainPath)) {
            throw new PluginException(sprintf('Textdomain %s file not found in %s.', $textDomain, $domainPath));
        }
    }

    /**
     * Actavation scripts.
     * 
     * Have to be loaded by register_activation_hook function on main plugin class.
     * 
     * @since 1.0.0
     */
    public function activate()
    {
    }

    /**
     * Deactavation scripts.
     * 
     * Have to be loaded by register_deactivation_hook function on main plugin class.
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
