<?php

class Ads {
	private $acfMinimumVersion = '5.0.0';

	/**
	 * ==============================================
	 *  __construct
	 * ==============================================
	 * Start your engines!
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	public function __construct() {
		// Check the ACF version and if its installed FIRST
		$validVersion = version_compare(get_option('acf_version'), $this->acfMinimumVersion, '>=');

		if ($validVersion) {
			// Register Actions
			add_action('init', array($this, 'pluginInit'));
			add_action('manage_simple_acf_ads_posts_custom_column', array($this, 'adminColumnsOutput'), 10, 2);
			add_action('admin_menu', array($this, 'addMenuPages'));

			// Regsiter Shortcodes
			add_shortcode('simple_acf_ads', array($this, 'outputAds'));

			// Filters
			add_filter('manage_simple_acf_ads_posts_columns', array($this, 'adminColumnsHeaders'));

		}

		// Only display the update nag
		else {
			add_action('admin_notices', array($this, 'notifyInstallOrUpdate'));
		}
	}


	/**
	 * ==============================================
	 *  pluginInit
	 * ==============================================
	 * Outputs ads via a shortcode
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	public function pluginInit() {
		// Creates post types
		register_post_type('simple_acf_ads',
			array(
				'labels'             => array(
					'name'          => __('Ads'),
					'singular_name' => __('Ad')
				),
				'public'             => true,
				'publicly_queryable' => false,
				'has_archive'        => false,
			)
		);

		// create a new taxonomy
		register_taxonomy(
			'simple_acf_ads_category',
			'simple_acf_ads',
			array(
				'label'             => __('Ad Categories'),
				'show_admin_column' => true,
				'hierarchical'      => true
			)
		);

		if (function_exists('acf_add_local_field_group')):

			acf_add_local_field_group(array(
				'key'                   => 'group_5be4409c6f10a',
				'title'                 => 'Ad Details',
				'fields'                => array(
					array(
						'key'               => 'field_5be440ac68874',
						'label'             => 'Start Date',
						'name'              => 'start_date',
						'type'              => 'date_picker',
						'instructions'      => 'The start date of this ad.',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '30',
							'class' => '',
							'id'    => '',
						),
						'display_format'    => 'F jS, Y',
						'return_format'     => 'Y-m-d',
						'first_day'         => 1,
					),
					array(
						'key'               => 'field_5be440d468875',
						'label'             => 'End Date',
						'name'              => 'end_date',
						'type'              => 'date_picker',
						'instructions'      => 'The end date of this ad.',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '30',
							'class' => '',
							'id'    => '',
						),
						'display_format'    => 'F jS, Y',
						'return_format'     => 'Y-m-d',
						'first_day'         => 1,
					),
					array(
						'key'               => 'field_5be4414868877',
						'label'             => 'Ad Type',
						'name'              => 'ad_type',
						'type'              => 'radio',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '40',
							'class' => '',
							'id'    => '',
						),
						'layout'            => 'horizontal',
						'choices'           => array(
							'image'        => 'Image',
							'script_embed' => 'Script Embed',
						),
						'default_value'     => 'image',
						'other_choice'      => 0,
						'save_other_choice' => 0,
						'allow_null'        => 0,
						'return_format'     => 'value',
					),
					array(
						'key'               => 'field_5be443076887b',
						'label'             => 'Ad Content',
						'name'              => 'ad_content',
						'type'              => 'textarea',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5be4414868877',
									'operator' => '==',
									'value'    => 'script_embed',
								),
							),
						),
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'delay'             => 0,
					),
					array(
						'key'               => 'field_5be442416887a',
						'label'             => 'Ad Image',
						'name'              => 'ad_image',
						'type'              => 'image',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5be4414868877',
									'operator' => '==',
									'value'    => 'image',
								),
							),
						),
						'wrapper'           => array(
							'width' => '33.3',
							'class' => '',
							'id'    => '',
						),
						'return_format'     => 'array',
						'preview_size'      => 'medium',
						'library'           => 'all',
						'min_width'         => '',
						'min_height'        => '',
						'min_size'          => '',
						'max_width'         => '',
						'max_height'        => '',
						'max_size'          => '',
						'mime_types'        => '',
					),
					array(
						'key'               => 'field_5be441f368878',
						'label'             => 'Ad URL',
						'name'              => 'ad_url',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5be4414868877',
									'operator' => '==',
									'value'    => 'image',
								),
							),
						),
						'wrapper'           => array(
							'width' => '33.3',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					),
					array(
						'key'               => 'field_5cba04e63be35',
						'label'             => 'Open in Separate Window?',
						'name'              => 'open_in_separate_window',
						'type'              => 'true_false',
						'instructions'      => 'Select this option to open the ad in a separate window.	Great for external Ads.',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5be4414868877',
									'operator' => '==',
									'value'    => 'image',
								),
							),
						),
						'wrapper'           => array(
							'width' => '33.3',
							'class' => '',
							'id'    => '',
						),
						'message'           => '',
						'default_value'     => 0,
						'ui'                => 0,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'simple_acf_ads',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => array(
					0 => 'permalink',
					1 => 'the_content',
					2 => 'featured_image',
				),
				'active'                => true,
				'description'           => '',
			));

		endif;
	}


	/**
	 * ==============================================
	 *  outputAds
	 * ==============================================
	 * Outputs ads via a shortcode
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	public function outputAds($atts) {
		$a = shortcode_atts(array(
			'category'       => '',
			'posts_per_page' => 1
		), $atts);

		// Get the necessary posts
		$today = current_time('Ymd');
		$args  = array(
			'post_type'      => 'simple_acf_ads',
			'posts_per_page' => $a['posts_per_page'],
			'orderby'        => 'rand',
			'meta_query'     => array(
				array(
					'key'     => 'start_date',
					'compare' => '<=',
					'value'   => $today,
				),
				array(
					'key'     => 'end_date',
					'compare' => '>=',
					'value'   => $today,
				)
			)
		);

		if (strlen($a['category']) > 0) {
			$categories        = explode(',', preg_replace('/\s+/', '', $a['category']));
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'simple_acf_ads_category',
					'field'    => 'slug',
					'terms'    => $categories
				)
			);
		}


		$ads = new WP_Query($args);

		ob_start();

		if ($ads->have_posts()) :
			while ($ads->have_posts()) :
				$ads->the_post();
				$adType   = get_field('ad_type');
				$adUrl    = get_field('ad_url');
				$external = get_field('open_in_separate_window');
				if ($external) {
					$external = ' target="_blank"';
				}

				else {
					$external = '';
				}
				?>
				<div class="simple-acf-ads <?php echo $adType; ?>">
					<?php
					switch ($adType) {
						case 'image' :
							$image = get_field('ad_image');
							?>
							<a href="<?php echo $adUrl; ?>" <?php echo $external; ?>>
								<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>"> </a>
							<?php
							break;
						case 'script_embed' :
							?>
							<div class="content-wrap">
								<?php the_field('ad_content'); ?>
							</div>
							<?php
							break;
					}
					?>
				</div>
			<?php
			endwhile;
			wp_reset_postdata();
		endif;

		return ob_get_clean();
	}


	/**
	 * ==============================================
	 *  adminColumnsOutput
	 * ==============================================
	 * Adds appropriate admin columns
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	function adminColumnsOutput($column, $post_id) {
		// Image column
		if ('image' == $column) {
			if (get_field('ad_type', $post_id) == 'image') {
				$image = get_field('ad_image', $post_id);
				echo '<img src="' . $image['sizes']['thumbnail'] . '">';
			}
		}

		if ('start_date' == $column) {
			echo date('F jS, Y', strtotime(get_field('start_date', $post_id)));

		}

		if ('end_date' == $column) {
			echo date('F jS, Y', strtotime(get_field('end_date', $post_id)));
		}

		if ('ad_type' == $column) {
			switch (get_field('ad_type', $post_id)) {
				case 'script_embed' :
					echo "Script Embed";
					break;
				case 'image' :
					echo "Image";
					break;
			}
		}
	}


	/**
	 * ==============================================
	 *  adminColumnsHeaders
	 * ==============================================
	 * Adds appropriate admin columns
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	function adminColumnsHeaders($columns) {
		//die(var_dump($columns));
		$columns = array(
			'cb'                               => $columns['cb'],
			'title'                            => __('Title'),
			'ad_type'                          => __('Ad Type'),
			'taxonomy-simple_acf_ads_category' => __('Ad Category'),
			'image'                            => __('Image'),
			'start_date'                       => __('Start Date'),
			'end_date'                         => __('End Date'),
		);

		return $columns;
	}


	/**
	 * ==============================================
	 *  notifyInstallOrUpdate
	 * ==============================================
	 * Displays admin upgrade nags!
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	public function notifyInstallOrUpdate() {
		$class   = 'notice notice-error';
		$message = __('You must have ACF 5.0 or greater installed to use the <strong>Simple ACF Ads</strong> plugin.', 'simpleacfads');

		printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
	}


	/**
	 * ==============================================
	 *  addMenuPages
	 * ==============================================
	 * Adds menu pages, especially the help
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	public function addMenuPages() {
		add_submenu_page(
			'edit.php?post_type=simple_acf_ads',
			'Simple ACF Help',
			'Help',
			'manage_options',
			'simple_acf_ads_help',
			array($this, 'renderHelpPage')
		);
	}


	/**
	 * ==============================================
	 *  renderHelpPage
	 * ==============================================
	 * Displays the Help Page
	 *
	 * @author         Chris Carvache
	 * @version        0.1
	 * @since          0.1
	 */

	public function renderHelpPage() {
		?>
		<div class="wrap">
			<h2><?php _e('Simple ACF Ads Help', 'require-featured-image') ?></h2>
			<p>Thank you for using Simple ACF Ads. We're happy that you've chosen us for your simple ad needs.</p>
			<p>Presently, using Simple ACF Ads removes around WordPress shortcodes.</p>

			<h4>Displaying ALL Ads:</h4>
			<code>[simple_acf_ads]</code>

			<h4>Displaying Ads by Category:</h4>
			<code>[simple_acf_ads category="my-category"]</code>
			<p>Make sure to use the categories slug. The shortcode will display nothing otherwise.</p>
		</div>
		<?php
	}
}
