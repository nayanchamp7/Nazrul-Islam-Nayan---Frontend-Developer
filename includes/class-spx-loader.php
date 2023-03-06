<?php
/**
 * SpaceX Main Class
 *
 * @category Class
 * @package  SPX_Loader
 * @author   Nazrul Islam Nayan <nazrulislamnayan7@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link     https://github.com/nayanchamp7/Nazrul-Islam-Nayan---Frontend-Developer.git
 */

defined('ABSPATH') || exit;

class SPX_Loader
{
    /**
     * SpaceX Main Class
     *
     * @category Class
     * @package  SPX_Loader
     * @author   Nazrul Islam Nayan <nazrulislamnayan7@gmail.com>
     * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
     * @link     https://github.com/nayanchamp7/Nazrul-Islam-Nayan---Frontend-Developer.git
     */

    protected static $_instance = null;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->includes();
        $this->hooks();
        do_action('spx_loaded', $this);
    }

    /**
     * Get the plugin version
     *
     * @return string|null
     */
    function version()
    {
        return esc_attr(SPX_VERSION);
    }

    /**
     * Define constance
     *
     * @param  $name
     * @param  $value
     * @return void
     */
    protected function define($name, $value)
    {
        if ( !defined($name) ) {
            define($name, $value);
        }
    }

    /**
     * Instance
     *
     * @return SPX_Loader|null
     */
    static function instance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Include files
     *
     * @return void
     */
    function includes()
    {
        // api file
        include dirname(__FILE__) . '/class-spx-api.php';

        // block file
        include dirname(__FILE__) . '/class-spx-block.php';

    }

    /**
     * Hook
     *
     * @return void
     */
    function hooks()
    {
        // init
        add_action('init', [$this, 'init'], 20);
    }

    /**
     * Initialize
     *
     * @return void
     */
    function init()
    {
        //api class initialize
        new SPX_API();

        //block class initialize
        $spx_block_obj = new SPX_Block();
        $spx_block_obj->get_loader($this);
    }

    /**
     * Load text domain
     *
     * @return void
     */
    public function language()
    {
        load_plugin_textdomain('spacex-craft', false, plugin_basename(dirname(SPX_PLUGIN_FILE)) . '/languages');
    }

    /**
     * Get file basename
     *
     * @return string
     */
    public function basename()
    {
        return basename(dirname(SPX_PLUGIN_FILE));
    }

    /**
     * Get plugin basename
     *
     * @return string
     */
    public function plugin_basename()
    {
        return plugin_basename(SPX_PLUGIN_FILE);
    }

    /**
     * Get plugin directory name
     *
     * @return string
     */
    public function plugin_dirname()
    {
        return dirname(plugin_basename(SPX_PLUGIN_FILE));
    }

    /**
     * Get plugin directory path
     *
     * @return string
     */
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(SPX_PLUGIN_FILE));
    }

    /**
     * Get plugin directory URL
     *
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', SPX_PLUGIN_FILE));
    }

    /**
     * Include folder path
     *
     * @param  $file string file path
     * @return string
     */
    function include_path($file = '')
    {
        return untrailingslashit(plugin_dir_path(SPX_PLUGIN_FILE) . 'includes') . $file;
    }

}
