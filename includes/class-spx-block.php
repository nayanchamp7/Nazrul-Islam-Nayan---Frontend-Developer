<?php

/**
 * SpaceX Block Class
 *
 * @category Class
 * @package  SPX_Block
 * @author   Nazrul Islam Nayan <nazrulislamnayan7@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link     https://github.com/nayanchamp7/Nazrul-Islam-Nayan---Frontend-Developer.git
 */

defined('ABSPATH') || exit;

class SPX_Block
{
    public $loader_obj = null;
    protected static $_instance = null;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->hooks();
        do_action('spx_block_loaded', $this);
    }

    /**
     * Instance
     *
     * @return SPX_Block|null
     */
    static function instance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Hook calling
     *
     * @return void
     */
    function hooks()
    {
        // init
        $this->init();

        // block scripts
        add_action('enqueue_block_editor_assets', [$this, 'block_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'block_scripts']);

        // add body class for login form
        add_filter('body_class', [$this, 'filter_body_class']);
    }

    /**
     * Initialize
     *
     * @return void
     */
    function init()
    {

        // register block
        register_block_type(
            plugin_dir_path(SPX_PLUGIN_FILE) . 'build',
            [
                'render_callback' => [$this, 'render_block'],
                'editor_script' => 'spx-block-editor-script',
                'script' => 'spx-block-script'
            ]
        );
    }

    /**
     * Get the main loader object
     *
     * @param  $loader_obj object loader object
     * @return $this
     */
    function get_loader($loader_obj)
    {

        $this->loader_obj = $loader_obj;

        return $this;
    }

    /**
     * Enqueue scripts
     *
     * @return void
     */
    function block_scripts()
    {

        // default dependencies
        $script_dependencies = array(
            'dependencies' => null,
            'version' => null,
        );

        // include dependencies file
        if ( file_exists($this->loader_obj->plugin_path() . '/build/index.asset.php') ) {
            $script_dependencies = include $this->loader_obj->plugin_path() . '/build/index.asset.php';
        }

        // block css
        wp_enqueue_style(
            'spx-blocks-style',
            $this->loader_obj->plugin_url() . '/build/style-index.css',
            array('wp-edit-blocks'),
            time()
        );

        // script file
        wp_register_script(
            'spx-block-editor-script',
            $this->loader_obj->plugin_url() . '/build/index.js',
            $script_dependencies['dependencies'],
            $script_dependencies['version'],
            true // Enqueue in the footer.
        );

        if( ! is_admin() ) {
            // script file
            wp_register_script(
                'spx-block-script',
                $this->loader_obj->plugin_url() . '/assets/public/js/script.js',
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
                    'is_admin' => is_admin(),
                    'plugin_url' => $this->loader_obj->plugin_url(),
                )
            );
        }
    }

    /**
     * Rendering the block
     *
     * @param  $attributes array block attributes
     * @param  $content    mixed block content
     * @return false|string
     */
    function render_block($attributes, $content)
    {

        ob_start();

        if( is_user_logged_in() ) {
            include $this->loader_obj->plugin_path() . '/template/content-spx-block.php';
        }else {
            //enqueue login styles
            wp_enqueue_style('login');

            //print login form
            echo wp_login_form();
        }

        return ob_get_clean();
    }

    /**
     * Filter the body class
     *
     * @param  $classes array body classes
     * @return mixed
     */
    function filter_body_class( $classes )
    {
        global $post;

        if ( has_block('spacex/craft', $post) ) {
            if( !is_user_logged_in() ) {
                $classes[] = 'login';
            }
        }

        return $classes;
    }
}
