<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://github.com:leandrogoncalves/vc_custom_carousel
 * @since      1.0.0
 *
 * @package    vc_custom_carousel
 * @subpackage vc_custom_carousel/core
 */

if(!defined('ABSPATH')) die('Wordpress is required');


class VcCustomCarousel {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Masonry_Grid_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;
	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (version_compare(PHP_VERSION, '5.5.0', '<')) {
			wp_die(__("This plugin require the PHP version 5.5.0 or later ", 'grp_plugin'));
		}
		$this->plugin_name = 'vc_custom_carousel';
		$this->version = '1.0.0';
		$this->site_url = get_site_url();
		$this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );

	}

	/**
	 * Get query args
	 * @return array|string
	 * @link http://php.net/manual/pt_BR/function.filter-input.php
	 */
	protected function getResults(array $filter = []){
		$default_fields = [
			'post_type'       => 'posts',
			'order'           => '',
			'orderby'         => '',
			'posts_per_page'  => '9',
			'paged'           => '1',
			'post_status'     => 'publish'
		];

		$filter = array_merge($default_fields, $filter);

		return  new WP_Query($filter);
	}

	/**
	 * Funcoes para registrar arquivo CSS
	 */

	protected function registerStyle(){
		wp_register_style( 'vc_cc_style', $this->plugin_path . "/css/style.css");
	}// fim do metodo

	/**
	 * Funcoes para enfileirar os estilos
	 */
	protected function enqueueStyles()
	{
		wp_enqueue_style('vc_cc_style');
	}// fim do metodo


	/**
	 * Fucao para registrar o shortcode
	 */
	protected function registerShortcode(){
		add_shortcode('vc_custom_carousel', array($this, 'shortcodeVcCustomCarousel'));
	}

	/**
	 * Shorcode para lsita de saidas em carrossel
	 */
	public function shortcodelistaSaidasCarrossel($attributes){
		$atts = shortcode_atts(array(
			'tags'            => '',
			'post_type'       => '',
			'order'           => '',
			'orderby'         => '',
			'posts_per_page'  => '',
			'paged'           => '',
			'post_status'     => '',
		), $attributes);

		$shortcode = null;

		$query_rs = $this->getResults($atts);

		if ($query_rs instanceof WP_Query && $query_rs->have_posts()):

			//AS TAGS ABAIXO SAO SHORTCODES DO PLUGIN VISUAL COMPOSER CONVERTIDAS PARA XML PARA FACILITAR O ENTENDIMENTO
			ob_start();
			?>
			<vc_row type="vc_default" css=".vc_custom_1474411094147{margin-right: 0px !important;margin-left: 0px !important;}">
				<vc_column>
					<ultimate_carousel title_text_typography="" slides_on_desk="3" slides_on_tabs="2" slides_on_mob="1" speed="600" autoplay="off" arrow_style="circle-bg" arrow_bg_color="rgba(202,202,202,0.7)" arrow_color="#ffffff" arrow_size="40" dots_color="#cacaca" adaptive_height="on" item_space="25" css_ad_caraousel=".vc_custom_1478285370868{margin-bottom: 0px !important;padding-right: 0px !important;padding-bottom: 0px !important;padding-left: 0px !important;}">
						<?php
						while ($query_rs->have_posts()) : $query_rs->the_post();

								$image = "";
								if (has_post_thumbnail( $query_rs->ID ) ):
									$image = wp_get_attachment_image_src( get_post_thumbnail_id( $query_rs->ID ), 'single-post-thumbnail' );
									if(isset($image[0])){
										$image = $image[0];
									}
								endif;

								$link = get_permalink($query_rs->ID);
								$title = $query_rs->post_title;
									?>
									<vc_row_inner css=".vc_custom_1478284419129{background-color: #e5e5e5 !important;}">
										<vc_column_inner el_class="cit_carousel_txtWrapper">
											#|a href="<?php echo $link ?>"|##|img class="img_cit_carousel" src="<?php echo $image ?>" alt="<?php echo $title ?>"|##|/a|#
											<vc_column_text css=".vc_custom_1478284352517{padding-top: 20px !important;padding-right: 20px !important;padding-bottom: 20px !important;padding-left: 20px !important;}">
												#|h4|#<?php echo get_field('data_extenso') ?>#|/h4|#
												#|h3|#<?php echo $title ?>#|/h3|#
												#|p|#<?php echo substr($query_rs->post_excerpt,0,100), '...' ?>#|/p|#
												#|h6|##|a href="<?php echo $link ?>"|# <?php echo _t("Veja mais") ?> #|/a|##|/h6|#
											</vc_column_text>
										</vc_column_inner>
									</vc_row_inner>
									<?php
						endwhile;
						?>
					</ultimate_carousel>
				</vc_column>
			</vc_row>
			<?php
			$shortcode = ob_get_clean();
		endif;

		echo do_shortcode($this->revert_tags($shortcode));
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->registerStyle();
		$this->enqueueStyles();
		$this->registerShortcode();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Convert shortcode tags to xml tags
	 *
	 * @param $content
	 * @return mixed
	 */
	public function invert_tags($content){
		$content =  str_replace('<','#|',$content );
		$content =  str_replace('>','|#',$content );
		$content =  str_replace('[','<',$content );
		$content =  str_replace(']','>',$content );
		return $content;
	}

	/**
	 * Convert xml tags to shortcode tags
	 *
	 * @param $content
	 * @return mixed
	 */
	public function revert_tags($content){
		$content =  str_replace('<','[',$content );
		$content =  str_replace('>',']',$content );
		$content =  str_replace('#|','<',$content );
		$content =  str_replace('|#','>',$content );
		return $content;
	}
}