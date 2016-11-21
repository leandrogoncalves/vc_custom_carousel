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
		$this->plugin_url = plugin_dir_url( __FILE__  );

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
	protected function registerStyle()
	{
		wp_register_style( 'vc_cc_style', $this->plugin_url . "css/style.css");
		wp_register_style( 'slick', $this->plugin_url . "css/slick.css");
		wp_register_style( 'slick-theme', $this->plugin_url . "css/slick-theme.css");
	}// fim do metodo

	/**
	 * Funcoes para enfileirar os estilos
	 */
	protected function enqueueStyles()
	{
		wp_enqueue_style('vc_cc_style');
		wp_enqueue_style('slick');
		wp_enqueue_style('slick-theme');
	}// fim do metodo

	/**
	 * Função para registra scripts
	 */
	protected function registerScripts()
	{
		wp_enqueue_script( 'slick-js', $this->plugin_url . 'js/slick.min.js',
			array( 'jquery' ),
			'3.3.7',
			true
		);
	}

	/**
	 * Funcoes para enfileirar os estilos
	 */
	protected function enqueueScripts(){
		wp_enqueue_script('jquery');
//		wp_enqueue_script('slick');
	}

	/**
	 * Fucao para registrar o shortcode
	 */
	protected function registerShortcode(){
		add_shortcode('vc_custom_carousel', array($this, 'shortcodeVcCustomCarousel'));
	}

	/**
	 * Shorcode para lsita de saidas em carrossel
	 */
	public function shortcodeVcCustomCarousel($attributes){
		$atts = shortcode_atts(array(
			'tags'            => '',
			'post_type'       => '',
			'order'           => '',
			'orderby'         => '',
			'posts_per_page'  => '',
			'paged'           => '',
			'post_status'     => '',
		), $attributes);

		global $post;
		$tags = [];

		$tags_per_post = wp_get_post_tags($post->ID,array( 'fields' => 'slugs' ));


		$shortcode = null;

		$query_rs = $this->getResults($atts);

		if ($query_rs instanceof WP_Query && $query_rs->have_posts()):

				ob_start();
				?>
				<div class="vc_cc">
					<?php
					while ($query_rs->have_posts()) : $query_rs->the_post();

						$encontrou = false;

						$the_ID = get_the_ID();
						$tags =  wp_get_post_tags($the_ID ,array( 'fields' => 'slugs' ));

						foreach ($tags_per_post as $tpp){
							foreach ($tags as $tag){
								if($tpp === $tag){
									$encontrou = true;
								}
							}
						}

					if($encontrou)
					{
						$image = "";
						if (has_post_thumbnail() ):
							$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'single-post-thumbnail' );
							if(isset($image[0])){
								$image = $image[0];
							}
						endif;

						$link = get_permalink();
						$title = get_the_title();
						$excerpt = get_the_excerpt();

						?>
							<div class="vc_cc_item">
								<div class="vc_cc_header" >
									<a href="<?php echo $link ?>"><div class="vc_cc_item_img" style="background-image: url('<?php echo $image ?>') " alt="<?php echo $title ?>;"></div></a>
								</div>
								<div class="vc_cc_body">
									<h4><?php echo get_field('data_extenso') ?></h4>
									<h3><?php echo $title ?></h3>
									<p><?php echo substr($excerpt,0,100), '...' ?></p>
									<h6><a href="<?php echo $link ?>"><?php echo _t("Veja mais") ?></a></h6>
								</div>
							</div>
						<?php
						$encontrou = false;
					}
						endwhile;
					?>
				</div>
				<script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
				<script type="text/javascript">
					$(document).ready(function() {
						$(".vc_cc").slick({
							dots: true,
							infinite: true,
							slidesToShow: 2,
							slidesToScroll: 2
						});
					});
				</script>
				<?php
				$shortcode = ob_get_clean();


		endif;

		echo $shortcode;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->registerStyle();
		$this->enqueueStyles();
		$this->registerScripts();
		$this->enqueueScripts();
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