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
			add_filter('the_content', [$this, 'insertAd'], 999, 1);

		}

		// Only display the update nag
		else {
			add_action('admin_notices', array($this, 'notifyInstallOrUpdate'));
		}
	}


	/**
	 * @param $the_content
	 *
	 * @return string|string[]
	 */
	public function insertAd($the_content) {
		if (is_singular('post')) {
			$auto_insert = get_field('auto_insert_into_post', 'option');
			$ad_mode = get_field('ad_mode');
			if (($ad_mode == 'override') || ($auto_insert && $ad_mode == 'default')) {
				$data_source = $ad_mode == 'override' ? get_the_ID() : 'option';

				$ad_zone = get_field('ad_zone', $data_source);
				$ad      = do_shortcode('[simple_acf_ads category="' . $ad_zone->slug . '"]');

				$direction = get_field('insert_direction', $data_source);
				$index     = get_field('insertion_count', $data_source);

				$start_pos       = $this->strpos_all($the_content, '</p>');
				$paragraph_count = count($start_pos);

				switch ($direction) {
					case 'beginning' :
						if ($index >= $paragraph_count)
							$index = $paragraph_count - 1;

						else
							$index--;

						break;
					case 'end':
						if ($index >= $paragraph_count)
							$index = 0;
						else {
							$index = ($paragraph_count - $index);
						}
						break;
				}

				$the_content = substr_replace($the_content, $ad, $start_pos[$index], 0);
			}

		}

		return $the_content;

	}

	/**
	 * Returns the position of all occurences in a string
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return array
	 */
	private function strpos_all($haystack, $needle) {
		$offset = 0;
		$allpos = array();
		while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
			$offset   = $pos + 1;
			$allpos[] = $pos;
		}
		return $allpos;
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

			acf_add_local_field_group(array(
				'key'                   => 'group_5f85a4476ce69',
				'title'                 => 'Simple ACF Ad Settings',
				'fields'                => array(
					array(
						'key'               => 'field_5f85a45bbadd1',
						'label'             => 'Auto Insert Into Post',
						'name'              => 'auto_insert_into_post',
						'type'              => 'true_false',
						'instructions'      => 'Automatically inserts ads into standard blog posts.',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'message'           => '',
						'default_value'     => 1,
						'ui'                => 1,
						'ui_on_text'        => 'Yes',
						'ui_off_text'       => 'No',
					),
					array(
						'key'                => 'field_5f85af66bee7b',
						'label'              => 'Ad Category',
						'name'               => 'ad_zone',
						'type'               => 'taxonomy',
						'instructions'       => '',
						'required'           => 0,
						'conditional_logic'  => array(
							array(
								array(
									'field'    => 'field_5f85a45bbadd1',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
						'wrapper'            => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'   => '',
						'taxonomy'           => 'simple_acf_ads_category',
						'field_type'         => 'select',
						'allow_null'         => 0,
						'add_term'           => 0,
						'save_terms'         => 0,
						'load_terms'         => 0,
						'return_format'      => 'object',
						'acfe_bidirectional' => array(
							'acfe_bidirectional_enabled' => '0',
						),
						'multiple'           => 0,
					),
					array(
						'key'               => 'field_5f85a66b90761',
						'label'             => 'Count from beginning or end of post',
						'name'              => 'insert_direction',
						'type'              => 'radio',
						'instructions'      => 'This option will determine which direction the count will start from, either from the beginning of the document or the end allowing better positioning of the ad.',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5f85a45bbadd1',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
						'wrapper'           => array(
							'width' => '50',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'choices'           => array(
							'beginning' => 'Start from the beginning',
							'end'       => 'Start from the end',
						),
						'allow_null'        => 0,
						'other_choice'      => 0,
						'default_value'     => 'beginning',
						'layout'            => 'vertical',
						'return_format'     => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key'               => 'field_5f85a70f90762',
						'label'             => 'Place after # paragraphs in post',
						'name'              => 'insertion_count',
						'type'              => 'number',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5f85a45bbadd1',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
						'wrapper'           => array(
							'width' => '50',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'default_value'     => 1,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'min'               => 1,
						'max'               => 50,
						'step'              => 1,
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'acf-options-settings',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'left',
				'instruction_placement' => 'field',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
				'acfe_display_title'    => '',
				'acfe_autosync'         => '',
				'acfe_permissions'      => '',
				'acfe_form'             => 0,
				'acfe_meta'             => '',
				'acfe_note'             => '',
			));
			acf_add_local_field_group(array(
				'key'                   => 'group_5f85b3b9e8e75',
				'title'                 => 'Ad Settings',
				'fields'                => array(
					array(
						'key'               => 'field_5f85b422c3685',
						'label'             => 'Ad Mode',
						'name'              => 'ad_mode',
						'type'              => 'radio',
						'instructions'      => 'Select the ad mode',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'choices'           => array(
							'default'  => 'Use the default global ad settings',
							'off'      => 'Shut off ads for this post',
							'override' => 'Override the global ad settings',
						),
						'allow_null'        => 0,
						'other_choice'      => 0,
						'default_value'     => 'default',
						'layout'            => 'vertical',
						'return_format'     => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key'                => 'field_5f85b3ba2105f',
						'label'              => 'Ad Category',
						'name'               => 'ad_zone',
						'type'               => 'taxonomy',
						'instructions'       => '',
						'required'           => 0,
						'conditional_logic'  => array(
							array(
								array(
									'field'    => 'field_5f85b422c3685',
									'operator' => '==',
									'value'    => 'override',
								),
							),
						),
						'wrapper'            => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'   => '',
						'taxonomy'           => 'simple_acf_ads_category',
						'field_type'         => 'select',
						'allow_null'         => 0,
						'add_term'           => 0,
						'save_terms'         => 0,
						'load_terms'         => 0,
						'return_format'      => 'object',
						'acfe_bidirectional' => array(
							'acfe_bidirectional_enabled' => '0',
						),
						'multiple'           => 0,
					),
					array(
						'key'               => 'field_5f85b3ba210a3',
						'label'             => 'Count from beginning or end of post',
						'name'              => 'insert_direction',
						'type'              => 'radio',
						'instructions'      => 'This option will determine which direction the count will start from, either from the beginning of the document or the end allowing better positioning of the ad.',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5f85b422c3685',
									'operator' => '==',
									'value'    => 'override',
								),
							),
						),
						'wrapper'           => array(
							'width' => '50',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'choices'           => array(
							'beginning' => 'Start from the beginning',
							'end'       => 'Start from the end',
						),
						'allow_null'        => 0,
						'other_choice'      => 0,
						'default_value'     => 'beginning',
						'layout'            => 'vertical',
						'return_format'     => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key'               => 'field_5f85b3ba210e4',
						'label'             => 'Place after # paragraphs in post',
						'name'              => 'insertion_count',
						'type'              => 'number',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_5f85b422c3685',
									'operator' => '==',
									'value'    => 'override',
								),
							),
						),
						'wrapper'           => array(
							'width' => '50',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'default_value'     => 1,
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'min'               => 1,
						'max'               => 5,
						'step'              => 1,
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'post',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'left',
				'instruction_placement' => 'field',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
				'acfe_display_title'    => '',
				'acfe_autosync'         => '',
				'acfe_permissions'      => '',
				'acfe_form'             => 0,
				'acfe_meta'             => '',
				'acfe_note'             => '',
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

		acf_add_options_sub_page(array(
			'page_title'  => 'Settings',
			'menu_title'  => 'Settings',
			'parent_slug' => 'edit.php?post_type=simple_acf_ads',
		));
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
