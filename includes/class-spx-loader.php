<?php

/**
 * SPX Main Class
 *
 * @package  SPX_Loader
 */

defined('ABSPATH') || exit;

class SPX_Loader
{
    protected static $_instance = null;

    // constructor
    function __construct()
    {
        $this->includes();
        $this->hooks();
        do_action('spx_loaded', $this);
    }

    // version
    function version()
    {
        return esc_attr(SPX_VERSION);
    }

    // define
    protected function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    // instance
    static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    // includes files
    function includes()
    {
        // api file including
        include dirname(__FILE__) . '/class-spx-api.php';

    }

    // action and filter hooks
    function hooks()
    {
        // init
        add_action('init', [$this, 'init']);
    }

    // plugin init
    function init()
    {
        //api class initialize
        new SPX_API();
    }

    public function language()
    {
        load_plugin_textdomain('spacex-craft', false, plugin_basename(dirname(SPX_PLUGIN_FILE)) . '/languages');
    }

    public function basename()
    {
        return basename(dirname(SPX_PLUGIN_FILE));
    }

    public function plugin_basename()
    {
        return plugin_basename(SPX_PLUGIN_FILE);
    }

    public function plugin_dirname()
    {
        return dirname(plugin_basename(SPX_PLUGIN_FILE));
    }

    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(SPX_PLUGIN_FILE));
    }

    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', SPX_PLUGIN_FILE));
    }

    function include_path($file = '')
    {
        return untrailingslashit(plugin_dir_path(SPX_PLUGIN_FILE) . 'includes') . $file;
    }
}
