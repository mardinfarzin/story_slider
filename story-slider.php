<?php
/**
 * Plugin Name: Circle Story Slider
 * Description: Create circular and rectangular sliders with categories, linking, and Elementor integration.
 * Version: 2
 * Author: Mardin Farzin
 * Text Domain: circle-story-slider
 */

// Prevent direct access to the plugin
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function circle_story_slider_enqueue_scripts() {
	// Add Slick styles
	wp_enqueue_style('slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css');
	wp_enqueue_style('slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css');

	// Add Slick script
	wp_enqueue_script('slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), null, true);

	// Add your plugin styles
	wp_enqueue_style('circle-story-slider-style', plugins_url('/assets/css/style.css', __FILE__));

	// Add your plugin scripts
	wp_enqueue_script('circle-story-slider-script', plugins_url('/assets/js/script.js', __FILE__), array('jquery', 'slick-js'), null, true);
}
add_action('wp_enqueue_scripts', 'circle_story_slider_enqueue_scripts');

// Create custom post type for slider categories
function circle_story_slider_custom_post_type() {
	register_post_type('slider_category',
		array(
			'labels' => array(
				'name' => __('Create Story'),
				'singular_name' => __('Create Story')
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array('title', 'thumbnail', 'editor', 'excerpt'),
			'menu_icon' => 'dashicons-slides',
		)
	);

	// Register taxonomy for slider category
	register_taxonomy('slider_category_taxonomy', 'slider_category', array(
		'labels' => array(
			'name' => __('Slider Categories', 'circle-story-slider'),
			'singular_name' => __('Slider Category', 'circle-story-slider'),
		),
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'slider-category'),
	));
}

add_action('init', 'circle_story_slider_custom_post_type');

// Add meta box for URL field
function add_slider_url_metabox() {
	add_meta_box(
		'slider_url_metabox', // Meta box ID
		__('Slider URL', 'circle-story-slider'), // Meta box title
		'display_slider_url_metabox', // Function to display the field
		'slider_category', // Post type
		'normal', // Position
		'default' // Priority
	);
}
add_action('add_meta_boxes', 'add_slider_url_metabox');

// Display the meta box
function display_slider_url_metabox($post) {
	$slider_url = get_post_meta($post->ID, '_slider_url', true);
	?>
    <label for="slider_url"><?php _e('Enter URL:', 'circle-story-slider'); ?></label>
    <input type="url" id="slider_url" name="slider_url" value="<?php echo esc_attr($slider_url); ?>" style="width: 100%;" />
	<?php
}

// Save URL from the meta box
function save_slider_url_metabox($post_id) {
	if (array_key_exists('slider_url', $_POST)) {
		update_post_meta(
			$post_id,
			'_slider_url',
			esc_url_raw($_POST['slider_url'])
		);
	}
}
add_action('save_post', 'save_slider_url_metabox');

// Create shortcode to display the slider
function circle_story_slider_shortcode($atts) {
	$atts = shortcode_atts(array(
		'category' => '',
		'post_type' => 'post',
		'autoplay_speed' => 3,
		'speed' => 500,
		'slides_to_show' => 3,
		'slides_to_scroll' => 1,
		'image_shape' => 'circle',
		'stories_per_slide' => -1,
		'order' => 'DESC',
		'show_excerpt' => 'no', // New parameter to display excerpt
		'use_internal_url' => 'yes', // New parameter to use internal links
	), $atts);

	$output = '<div class="circle-story-slider" 
		data-autoplay-speed="' . esc_attr($atts['autoplay_speed']) . '" 
		data-speed="' . esc_attr($atts['speed']) . '" 
		data-slides-to-show="' . esc_attr($atts['slides_to_show']) . '" 
		data-slides-to-scroll="' . esc_attr($atts['slides_to_scroll']) . '">';

	$query_args = array(
		'post_type' => $atts['post_type'],
		'posts_per_page' => $atts['stories_per_slide'],
		'order' => $atts['order'],
		'post_status' => 'publish',
	);

	if (!empty($atts['category'])) {
		// If post_type is slider, use custom taxonomy
		if ($atts['post_type'] === 'slider_category') {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'slider_category_taxonomy', // Custom taxonomy for slider
					'field' => 'slug',
					'terms' => $atts['category'],
				),
			);
		} else {
			// For other post types, use the default category
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'category', // Default taxonomy for other post types
					'field' => 'slug',
					'terms' => $atts['category'],
				),
			);
		}
	}


	$query = new WP_Query($query_args);

	while ($query->have_posts()) : $query->the_post();
		// Get internal or external link
		$post_meta_url = get_post_meta(get_the_ID(), '_slider_url', true);
		$post_link = ($atts['use_internal_url'] === 'no' && !empty($post_meta_url)) ? esc_url($post_meta_url) : get_permalink();

		$output .= '<div class="slider-item">';
		$output .= '<a href="' . $post_link . '" target="_blank">'; // Use internal or metadata link
		$output .= '<div class="slider-image ' . esc_attr($atts['image_shape']) . '">';
		$output .= get_the_post_thumbnail(get_the_ID(), 'thumbnail');
		$output .= '</div>';
		$output .= '<p class="slider-title">' . get_the_title() . '</p>';

		if ($atts['show_excerpt'] === 'yes') {
			$output .= '<p class="slider-excerpt">' . get_the_excerpt() . '</p>';
		}

		$output .= '</a>';
		$output .= '</div>';
	endwhile;

	wp_reset_postdata();
	$output .= '</div>';

	return $output;
}
add_shortcode('circle_slider', 'circle_story_slider_shortcode');


// Register widget for Elementor
function register_circle_slider_widget($widgets_manager) {
    require_once(__DIR__ . '/widgets/circle-slider-widget.php');
    $widgets_manager->register(new \Elementor_Circle_Slider_Widget());
}
add_action('elementor/widgets/register', 'register_circle_slider_widget');
