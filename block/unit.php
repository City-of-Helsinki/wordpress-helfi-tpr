<?php

namespace CityOfHelsinki\WordPress\TPR\Blocks;

use ArtCloud\Helsinki\Plugin\HDS\Svg;
use CityOfHelsinki\WordPress\TPR as Plugin;
use CityOfHelsinki\WordPress\TPR\Api\Units;
use CityOfHelsinki\WordPress\TPR\Api\Entities\Unit;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Connection;

use WP_Block_Editor_Context;

/**
  * Config
  */
function blocks() {
	return array(
		'unit' => array(
			'title' => __( 'Helsinki - Unit (TPR)', 'helsinki-tpr' ),
			'category' => 'helsinki-tpr',
			'dependencies' => array(
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-components',
				'wp-editor',
				'wp-compose',
				'wp-data',
				'wp-server-side-render',
			),
			'render_callback' => __NAMESPACE__ . '\\render_unit',
			'attributes' => array(
				'postID' => array(
					'type' => 'string',
					'default' => 0,
				),
				'unitTitle' => array(
					'type' => 'string',
					'default' => '',
				),
				'showStreetAddress' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showPostalAddress' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showPhone' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showEmail' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showOpenHours' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showWebsite' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showAdditionalInfo' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showPhoto' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'anchor' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);
}

/**
  * Register
  */
add_action( 'init', __NAMESPACE__ . '\\register' );
function register() {
	foreach ( blocks() as $block => $config ) {
		register_block_type( "helsinki-tpr/{$block}", $config );
	}
}

add_filter( 'block_categories_all', __NAMESPACE__ . '\\category', 10, 2 );
function category( $categories, $context ) {
	if ( ! ( $context instanceof WP_Block_Editor_Context ) ) {
		return $categories;
	}

	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'helsinki-tpr',
				'title' => __( 'Helsinki', 'helsinki-tpr' ),
				'icon'  => 'building',
			),
		)
	);
}

/**
  * Assets
  */
/*function block_dependencies() {
	$dependencies = array();
	foreach ( blocks() as $block => $config ) {
		$dependencies = array_merge(
			$dependencies,
			$config['dependencies']
		);
	}
	return array_unique( $dependencies, SORT_STRING );
}*/


add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_assets', 10 );
function admin_assets( string $hook ) {
	//if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        //return;
    //}

	$base = Plugin\plugin_url();
	$debug = Plugin\debug_enabled();
	$version = $debug ? time() : Plugin\PLUGIN_VERSION;

	wp_enqueue_script(
		'helsinki-tpr-scripts',
		$debug ? $base . 'assets/admin/js/scripts.js' : $base . 'assets/admin/js/scripts.min.js',
		array(),
		$version,
		true
	);

	wp_localize_script(
		'helsinki-tpr-scripts',
		'helsinkiTPR',
        array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

	wp_set_script_translations(
        'helsinki-tpr-scripts',
        'helsinki-tpr',
        Plugin\plugin_path() . 'languages'
    );

	wp_enqueue_style(
		'helsinki-tpr-tyles',
		$debug ? $base . 'assets/admin/css/styles.css' : $base . 'assets/admin/css/styles.min.css',
		array(),
		$version,
		'all'
	);
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\public_assets', 1 );
function public_assets() {
	$base = Plugin\plugin_url();
	$debug = Plugin\debug_enabled();
	$version = $debug ? time() : Plugin\PLUGIN_VERSION;

	wp_enqueue_script(
		'helsinki-tpr-scripts',
		$debug ? $base . 'assets/public/js/scripts.js' : $base . 'assets/public/js/scripts.min.js',
		array(),
		$version,
		true
	);

	wp_localize_script(
		'helsinki-tpr-scripts',
		'helsinkiTPR',
        array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

	wp_enqueue_style(
		'helsinki-tpr-styles',
		$debug ? $base . 'assets/public/css/styles.css' : $base . 'assets/public/css/styles.min.css',
		array( 'wp-block-library' ),
		$version,
		'all'
	);
}

/**
  * Rendering
  */
function render_unit( $attributes ) {
	if ( empty( $attributes['postID'] ) ) {
		return;
	}

	$unit = Units::entities( absint($attributes['postID']) );
	if ( ! $unit ) {
		return;
	}

	$id = '';
	if (!empty($attributes['anchor'])) {
		$id = 'id="'.esc_attr($attributes['anchor']).'"';
	}

	$name = '';
	if (!empty($attributes['unitTitle'])) {
		$name = $attributes['unitTitle'];
	} else {
		$name = $unit->name();
	}

	return sprintf(
		'<div %s class="helsinki-tpr tpr-unit">
			<div class="hds-container">
				%s
				<div class="tpr__container">%s</div>
			</div>
		</div>',
		$id,
		render_unit_title( $name ?? '', $attributes['postID'] ),
		render_unit_data( $unit, $attributes )
	);
}

function render_unit_title( string $title, int $postID ) {
	return apply_filters(
		'helsinki_tpr_unit_block_title',
		sprintf(
			'<h2 class="unit__title">%s</h2>',
			esc_html( $title )
		),
		$title,
		$postID
	);
}

function render_unit_data( $unit, $attributes ) {
	return apply_filters(
		'helsinki_tpr_unit_data',
		sprintf(
			'%s
			%s',
			render_unit_main_column($unit, $attributes),
			render_unit_secondary_column($unit, $attributes)
		),
		$unit,
		$attributes
	);
}

function render_unit_main_column($unit, $attributes) {
	$data = array();

	if (isset($attributes['showStreetAddress']) && $attributes['showStreetAddress']) {
		$data[] = render_unit_street_address($unit);
	}
	if (isset($attributes['showPhoto']) && $attributes['showPhoto']) {
		$data[] = render_unit_image($unit, 'show-on-mobile');
	}
	if (isset($attributes['showPostalAddress']) && $attributes['showPostalAddress']) {
		$data[] = render_unit_postal_address($unit);
	}
	if (isset($attributes['showPhone']) && $attributes['showPhone']) {
		$data[] = render_unit_phone_number($unit);
	}
	if (isset($attributes['showEmail']) && $attributes['showEmail']) {
		$data[] = render_unit_email($unit);
	}
	if (isset($attributes['showOpenHours']) && $attributes['showOpenHours']) {
		$data[] = render_unit_open_hours($unit);
	}
	if (isset($attributes['showWebsite']) && $attributes['showWebsite']) {
		$data[] = render_unit_website($unit);
	}
	if (isset($attributes['showAdditionalInfo']) && $attributes['showAdditionalInfo']) {
		$data[] = render_unit_additional_info($unit, 'show-on-mobile');
	}

	return sprintf('<div class="tpr__container__column tpr__main_column">%s</div>', implode('', apply_filters(
		'helsinki_tpr_unit_main_elements',
		$data,
		$unit, $attributes
	)));
}

function render_unit_secondary_column($unit, $attributes) {
	$data = array();

	if (isset($attributes['showPhoto']) && $attributes['showPhoto']) {
		$data[] = render_unit_image($unit);
	}
	if (isset($attributes['showAdditionalInfo']) && $attributes['showAdditionalInfo']) {
		$data[] = render_unit_additional_info($unit);
	}

	$html = implode('', apply_filters(
		'helsinki_tpr_unit_secondary_elements',
		$data,
		$unit, $attributes
	));

	if (!$html) {
		return '';
	}

	return sprintf('<div class="tpr__container__column tpr__secondary_column">%s</div>', $html);
}

function render_unit_section_title($title, $iconType, $icon) {
	return sprintf('<div class="unit__section_title">%s<div>%s:</div></div>', render_unit_icon($iconType, $icon), $title);
}

function render_unit_icon($type, $icon) {
	return class_exists(Svg::class) ? Svg::icon($type, $icon) : '';
}

function render_unit_street_address($unit) {
	$parts = array();
	if (!empty($unit->street_address())) {
		if (!empty($unit->address_zip()) || !empty($unit->address_city())) {
			$parts[] = sprintf('%s,', $unit->street_address());
		} else {
			$parts[] = sprintf('%s', $unit->street_address());
		}
	}
	if (!empty($unit->address_zip())) {
		$parts[] = sprintf('%s', $unit->address_zip());
	}
	if (!empty($unit->address_city())) {
		$parts[] = sprintf('%s', $unit->address_city());
	}

	return sprintf('<div class="unit__street_address">%s<div class="unit__section_data">%s<p></p>%s</div></div>',
		render_unit_section_title(__('Street address', 'helsinki-tpr'), 'blocks', 'location'),
		render_unit_service_map_link($unit, implode(' ', $parts)),
		render_unit_hsl_route_link($unit)
	);
}

function render_unit_service_map_link($unit, $text) {
	$map_link = $unit->get_service_map_link();
	return sprintf('<p><a href="%s">%s</a></p>',
		$map_link,
		$text,
	);
}

function render_unit_hsl_route_link($unit) {
	$route_link = $unit->get_hsl_route_link();
	if (!$route_link) {
		return '';
	}
	return sprintf('<p class="unit__link"><a href="%s">%s</a></p>',
		$route_link,
		__('Show route in the HSL Journey Planner', 'helsinki-tpr'),
	);
}


function render_unit_postal_address($unit) {
	$address = $unit->postal_address();
	if (!$address) {
		return '';
	}

	return sprintf('<div class="unit__postal_address">%s<div class="unit__section_data"><p>%s</p></div></div>',
		render_unit_section_title(__('Postal address', 'helsinki-tpr'), 'blocks', 'location'),
		$unit->postal_address(),
	);
}

function render_unit_phone_number($unit) {
	$phone = $unit->phone();
	if (!$phone) {
		return '';
	}
	return sprintf('<div class="unit__phone">%s<div class="unit__section_data"><p><a href="tel:%s">%s</a></p></div></div>',
		render_unit_section_title(__('Phonenumber', 'helsinki-tpr'), 'blocks', 'phone'),
		$phone,
		$phone,
	);
}

function render_unit_email($unit) {
	$email = $unit->email();
	if (!$email) {
		return '';
	}
	return sprintf('<div class="unit__email">%s<div class="unit__section_data"><p><a href="mailto:%s">%s</a></p></div></div>',
		render_unit_section_title(__('Email', 'helsinki-tpr'), 'blocks', 'envelope'),
		$email,
		$email,
	);
}

function render_unit_open_hours( Unit $unit ): string {
	if ( ! $unit->open_hours() ) {
		return '';
	}

	$current_lang = function_exists( 'pll_default_language' )
		? pll_default_language()
		: get_locale();

	return sprintf(
		'<div class="unit__open_hours">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title( __('Open hours', 'helsinki-tpr'), 'blocks', 'clock' ),
		implode( '', $unit->open_hours_html( $current_lang ) ),
	);
}

function render_unit_website($unit) {
	$weburl = $unit->website_url();
	if (!$weburl) {
		return '';
	}
	return sprintf('<div class="unit__website">%s<div class="unit__section_data"><p><a href="%s">%s</a></p></div></div>',
		render_unit_section_title(__('Website link', 'helsinki-tpr'), 'blocks', 'arrow-right'),
		$weburl,
		$weburl,
	);
}

function render_unit_image($unit, $extra_classes = '') {
	$image = $unit->html_img();

	if (!$image) {
		return '';
	}

	return sprintf('<div class="unit__image %s">%s</div>',
		$extra_classes,
		$image,
	);
}

function render_unit_additional_info($unit, $extra_classes = '') {
	$info = $unit->additional_info();
	if (!$info) {
		return '';
	}
	return sprintf('<div class="unit__additional_info %s">%s<div class="unit__section_data">%s</div></div>',
		$extra_classes,
		render_unit_section_title(__('Additional information', 'helsinki-tpr'), 'blocks', 'info-circle'),
		'<p>' . implode('</p><p>', $unit->additional_info()) . '</p>',
	);

}
