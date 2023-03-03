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

        // block scripts
        add_action('enqueue_block_editor_assets', [$this, 'spx_block_scripts']);
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

    // enqueue scripts
    function spx_block_scripts()
    {

        // default dependencies
        $script_dependencies = array(
            'dependencies' => null,
            'version' => null,
        );

        // include dependencies file
        if ( file_exists($this->plugin_path() . '/build/index.asset.php') ) {
            $script_dependencies = require $this->plugin_path() . '/build/index.asset.php';
        }

        // block css
        wp_enqueue_style(
            'spx-blocks-style',
            $this->plugin_url() . '/build/style-index.css',
            array('wp-edit-blocks'),
            time()
        );

        // script file
        wp_register_script(
            'spx-block-script',
            $this->plugin_url() . '/build/index.js',
            $script_dependencies['dependencies'],
            $script_dependencies['version'],
            true // Enqueue in the footer.
        );

        // localize script
        wp_localize_script(
            'spx-block-script',
            'spx_script_obj',
            array(
                'homeurl' => home_url(),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'plugin_url' => $this->plugin_url(),
            )
        );

        // register block
        register_block_type(
            $this->plugin_path(),
            array(
                'editor_script' => 'spx-block-script'
            )
        );
    }
}
