<?php
class Elementor_Circle_Slider_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'circle_slider_widget';
	}

	public function get_title() {
		return __('Circle Slider', 'circle-story-slider');
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_categories() {
		return ['general'];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Slider Settings', 'circle-story-slider'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// Slider category selection
		$this->add_control(
			'category',
			[
				'label' => __('Category', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_slider_categories(),
				'default' => '',
			]
		);

		// Post type selection
		$this->add_control(
			'post_type',
			[
				'label' => __('Post Type', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_post_types(),
				'default' => 'post', // Default to post
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label' => __('Show Excerpt', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'circle-story-slider'),
				'label_off' => __('No', 'circle-story-slider'),
				'default' => 'no',
			]
		);

		// Autoplay speed setting
		$this->add_control(
			'autoplay_speed',
			[
				'label' => __('Autoplay Speed (seconds)', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			]
		);

		// Slider transition speed setting
		$this->add_control(
			'speed',
			[
				'label' => __('Transition Speed (seconds)', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 5,  // Default value
			]
		);

		// Number of slides shown per page
		$this->add_control(
			'slides_to_show',
			[
				'label' => __('Number of Slides Per Page', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			]
		);

		// Number of slides scrolled
		$this->add_control(
			'slides_to_scroll',
			[
				'label' => __('Number of Slides Scrolled', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		// Image display type
		$this->add_control(
			'image_shape',
			[
				'label' => __('Image Shape', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'circle' => __('Circle', 'circle-story-slider'),
					'rectangle' => __('Rectangle', 'circle-story-slider'),
				],
				'default' => 'circle',  // Default to circle
			]
		);

		// Number of stories per slide
		$this->add_control(
			'stories_per_slide',
			[
				'label' => __('Number of Stories Per Slide', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '',
				'description' => __('Leave empty to display all stories.', 'circle-story-slider'),
			]
		);

		// Post display order
		$this->add_control(
			'order',
			[
				'label' => __('Post Order', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'ASC' => __('Ascending', 'circle-story-slider'),
					'DESC' => __('Descending', 'circle-story-slider'),
				],
				'default' => 'DESC',
			]
		);

		// Use internal URL toggle
		$this->add_control(
			'use_internal_url',
			[
				'label' => __('Use Internal URL', 'circle-story-slider'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'circle-story-slider'),
				'label_off' => __('No', 'circle-story-slider'),
				'default' => 'no',
			]
		);

		$this->end_controls_section();
	}

	protected function get_slider_categories() {
		$taxonomies = ['category', 'slider_category_taxonomy', 'product_cat'];
		$valid_taxonomies = [];

		// Check if taxonomies exist
		foreach ($taxonomies as $taxonomy) {
			if (taxonomy_exists($taxonomy)) {
				$valid_taxonomies[] = $taxonomy;
			}
		}

		// If no valid taxonomies are found
		if (empty($valid_taxonomies)) {
			return ['' => __('No categories available', 'circle-story-slider')];
		}

		// Fetch terms for valid taxonomies
		$terms = get_terms(['hide_empty' => false]);
		$options = ['' => __('No categories available', 'circle-story-slider')]; // Add empty option

		foreach ($terms as $term) {
			$options[$term->slug] = $term->name;
		}
		return $options;
	}

	protected function get_post_types() {
		$post_types = get_post_types(['public' => true], 'names');
		$options = [];
		foreach ($post_types as $post_type) {
			if (($post_type == "attachment") || ($post_type == "e-floating-buttons") || ($post_type == "elementor_library")) {
				continue;
			}
			$options[$post_type] = ucfirst($post_type);
		}
		return $options;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$stories_per_slide = !empty($settings['stories_per_slide']) ? $settings['stories_per_slide'] : -1; // Show all stories if empty
		$show_excerpt = $settings['show_excerpt'] === 'yes';
		$use_internal_url = $settings['use_internal_url'] === 'yes' ? 'yes' : 'no'; // Check for internal URL usage

		echo do_shortcode('[circle_slider category="' . $settings['category']
		                  . '" post_type="' . $settings['post_type']
		                  . '" autoplay_speed="' . $settings['autoplay_speed']
		                  . '" speed="' . $settings['speed']
		                  . '" slides_to_show="' . $settings['slides_to_show']
		                  . '" slides_to_scroll="' . $settings['slides_to_scroll']
		                  . '" image_shape="' . $settings['image_shape']
		                  . '" stories_per_slide="' . $stories_per_slide
		                  . '" order="' . $settings['order']
		                  . '" show_excerpt="' . ($show_excerpt ? 'yes' : 'no')
		                  . '" use_internal_url="' . $use_internal_url . '"]');
	}
}

?>
