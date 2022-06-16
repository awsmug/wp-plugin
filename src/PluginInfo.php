<?php

namespace AWSM\WP_Plugin;

/**
 * Plugin information loader from plugin header splitted into methods.
 * 
 * @since 1.0.0
 */
class PluginInfo
{
    /**
     * Plugin file.
     * 
     * @var string
     * 
     * @since 1.0.0
     */
    private $pluginFile;

    /**
     * Plugin data.
     * 
     * @var array
     * 
     * @since 1.0.0
     */
    private $pluginData = [];

    /**
     * Constructor
     * 
     * @param string $pluginFile Plugin starter file which contains plugin comments.
     * 
     * @since 1.0.0
     */
    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->load();
    }

    /**
     * Loading plugin data
     * 
     * @since 1.0.0
     */
    private function load()
    {
        $this->pluginData = $this->getPluginData();
    }

    /**
     * Get Name parameter
     * 
     * @return string Name of the plugin.
     * 
     * @since 1.0.0
     */
    public function getName(): string
    {
        return $this->pluginData['Name'];
    }

    /**
     * Get Title parameter
     * 
     * @return string Title of the plugin and link to the plugin's site.
     * 
     * @since 1.0.0
     */
    public function getTitle(): string
    {
        return $this->pluginData['Title'];
    }

    /**
     * Get Description parameter
     * 
     * @return string Plugin description.
     * 
     * @since 1.0.0
     */
    public function getDescription(): string
    {
        return $this->pluginData['Description'];
    }

    /**
     * Get Author parameter
     * 
     * @return string Author's name.
     * 
     * @since 1.0.0
     */
    public function getAuthor(): string
    {
        return $this->pluginData['Author'];
    }

    /**
     * Get AuthorURI parameter
     * 
     * @return string Author's website address. 
     * 
     * @since 1.0.0
     */
    public function getAuthorURI(): string
    {
        return $this->pluginData['AuthorURI'];
    }

    /**
     * Get Version parameter
     * 
     * @since 1.0.0
     */
    public function getVersion(): string
    {
        return $this->pluginData['Version'];
    }

    /**
     * Get TextDomain parameter
     * 
     * @return string Plugin textdomain.
     * 
     * @since 1.0.0
     */
    public function getTextDomain(): string
    {
        return $this->pluginData['TextDomain'];
    }

    /**
     * Get DomainPath parameter
     * 
     * @return string Plugins relative directory path to .mo files.
     * 
     * @since 1.0.0
     */
    public function getDomainPath(): string
    {
        return $this->pluginData['DomainPath'];
    }

    /**
     * Get TemplatePath parameter
     * 
     * @return string Template relative to the plugin directory.
     * 
     * @since 1.0.0
     */
    public function getTemplatePath(): string
    {
        return trailingslashit($this->pluginData['TemplatePath']);
    }

    /**
     * Get Network parameter
     * 
     * @return bool Whether the plugin can only be activated network-wide.
     * 
     * @since 1.0.0
     */
    public function getNetwork(): bool
    {
        return $this->pluginData['Network'];
    }

    /**
     * Get RequiresWP parameter
     * 
     * @return bool Minimum required version of WordPress.
     * 
     * @since 1.0.0
     */
    public function getRequiredWPVersion(): string
    {
        return $this->pluginData['RequiresWP'];
    }

    /**
     * Get RequiresPHP parameter
     * 
     * @return bool Minimum required version of PHP.
     * 
     * @since 1.0.0
     */
    public function getRequiredPHPVersion(): string
    {
        return $this->pluginData['RequiresPHP'];
    }

    /**
     * Get plugin data (copied and modified form get_pluginData WP function)
     * 
     * @param string $plugin_file
     * 
     * @since 1.0.0
     */
    private function getPluginData()
    {
        $defaultHeaders = array(
            'Name'         => 'Plugin Name',
            'PluginURI'    => 'Plugin URI',
            'Version'      => 'Version',
            'Description'  => 'Description',
            'Author'       => 'Author',
            'AuthorURI'    => 'Author URI',
            'TextDomain'   => 'Text Domain',
            'DomainPath'   => 'Domain Path',
            'TemplatePath' => 'Template Path',
            'Network'      => 'Network',
            'RequiresWP'   => 'Requires at least',
            'RequiresPHP'  => 'Requires PHP'
        );

        $pluginData = $this->getFileData($this->pluginFile, $defaultHeaders);

        $pluginData['Network'] = ('true' === strtolower($pluginData['Network']));
        unset($pluginData['_sitewide']);

        $pluginData['Title']      = $pluginData['Name'];
        $pluginData['AuthorName'] = $pluginData['Author'];

        return $pluginData;
    }

    /**
     * Get plugin data (copied and modified form get_pluginData WP function)
     * 
     * @param string $file
     * @param array  $defaultHeaders
     * 
     * @since 1.0.0
     */
    private function getFileData($file, $defaultHeaders)
    {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen($file, 'r');

        // Pull only the first 8 KB of the file in.
        $fileData = fread($fp, 8 * 1024);

        // PHP will close file handle, but we are good citizens.
        fclose($fp);

        // Make sure we catch CR-only line endings.
        $fileData = str_replace("\r", "\n", $fileData);

        $allHeaders = $defaultHeaders;

        foreach ($allHeaders as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $fileData, $match) && $match[1]) {
                $allHeaders[$field] = trim(preg_replace('/\s*(?:\*\/|\?>).*/', '', $match[1]));
            } else {
                $allHeaders[$field] = '';
            }
        }

        return $allHeaders;
    }

    /**
     * Get plugin path.
     * 
     * @return string Plugin path.
     * 
     * @since 1.0.0
     */
    public function getPath()
    {
        return dirname($this->pluginFile);
    }

    /**
     * Get plugin url.
     * 
     * @return string Plugin url.
     * 
     * @since 1.0.0
     */
    public function getUrl(): string
    {
        return plugin_dir_url($this->pluginFile);
    }
}
