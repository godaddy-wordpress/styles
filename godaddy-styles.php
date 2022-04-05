<?php
/**
 * Plugin Name: GoDaddy WordPress Styles
 * Plugin URI: https://godaddy.com/
 * Description: GoDaddy WordPress Styles Description
 * Version: 0.0.1
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author: GoDaddy
 * Author URI: https://godaddy.com
 * Text Domain: godaddy-styles
 * Domain Path: /languages
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 * @package GoDaddy_Styles
 */

defined( 'ABSPATH' ) || exit;

define( 'GODADDY_STYLES_VERSION', '0.0.1' );
define( 'GODADDY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GODADDY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function godaddy_styles_enqueue() {
	wp_enqueue_style(
		'godaddy-styles',
		GODADDY_PLUGIN_URL . 'css/styles.css',
		array( 'wp-components' ),
		GODADDY_STYLES_VERSION
	);
}

add_action( 'admin_enqueue_scripts', 'godaddy_styles_enqueue' );
add_action( 'wp_enqueue_scripts', 'godaddy_styles_enqueue' );