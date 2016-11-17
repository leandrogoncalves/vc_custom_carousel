<?php
/**
 * @link              https://github.com:leandrogoncalves/vc_custom_carousel
 * @since             1.0.0
 * @package           vc_custom_carousel
 *
 * @wordpress-plugin
 * Plugin Name:       VC Custom_Carousel
 * Plugin URI:        https://github.com:leandrogoncalves/vc_custom_carousel
 * Description:       Wordpress plugin for implements a custom carrousel from any post types
 * Version:           1.0.0
 * Author:            Leandro Gonçavlves <contato.Leandro Goncalves@gmail.com>
 * Author URI:        https://github.com/leandrogoncalves
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       vc-custom-carousel
 * Domain Path:       /languages
 */
if ( ! defined( 'WPINC' ) ) {
	die('WP precisa ser inicializado');
}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'src/VcCustomCarousel.php';
$plugin = new VcCustomCarousel();
$plugin->run();