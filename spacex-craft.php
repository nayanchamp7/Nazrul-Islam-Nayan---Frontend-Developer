<?php
/**
 * Plugin Name:       SpaceX Craft
 * Description:       A simple gutenberg block plugin to show spacex craft.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Nazrul Islam Nayan
 * Author URI:        https://profiles.wordpress.org/nayanchamp7/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       spacex-craft
 *
 * @category Base
 * @package  SpaceX
 * @author   Nazrul Islam Nayan <nazrulislamnayan7@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link     https://github.com/nayanchamp7/Nazrul-Islam-Nayan---Frontend-Developer.git
 */


defined('ABSPATH') || exit;

if (! defined('SPX_VERSION') ) {
    define('SPX_VERSION', '1.0.0');
}

if (! defined('SPX_PLUGIN_FILE') ) {
    define('SPX_PLUGIN_FILE', __FILE__);
}

// Include the main class.
if (! class_exists('SPX_Loader', false) ) {
    include_once dirname(__FILE__) . '/includes/class-spx-loader.php';
}

/**
 * Returns the main instance.
 *
 * @return object instance of the base file
 */
function spx_loader_callback()  // phpcs:ignore
{
    return SPX_Loader::instance();
}

add_action('plugins_loaded', 'spx_loader_callback'); // phpcs:ignore