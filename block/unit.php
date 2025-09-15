<?php

namespace CityOfHelsinki\WordPress\TPR\Blocks;

use CityOfHelsinki\WordPress\TPR as Plugin;
use CityOfHelsinki\WordPress\TPR\Api\Units;
use CityOfHelsinki\WordPress\TPR\Api\Entities\Unit;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Connection;

/**
  * Register
  */
\add_action( 'init', __NAMESPACE__ . '\\register' );
function register(): void {
	\register_block_type(
		\plugin_dir_path( __FILE__ ) . 'block.json',
		array( 'render_callback' => __NAMESPACE__ . '\\render_unit' )
	);

	\add_filter( 'helsinki_tpr_current_language', __NAMESPACE__ . '\\determine_current_language' );
}

function determine_current_language( string $language ): string {
	return array_reduce(
		array( 'pll_current_language', 'pll_default_language' ),
		function( string $current, $handler ) {
			if ( function_exists( $handler ) ) {
				$current = call_user_func( $handler ) ?: $current;
			}

			return $current;
		},
		$language
	);
}

\add_filter( 'block_categories_all', __NAMESPACE__ . '\\category', 10, 2 );
function category( array $categories, $context ): array {
	if ( $context instanceof \WP_Block_Editor_Context ) {
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

	return $categories;
}

\add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_assets', 10 );
function admin_assets( string $hook ): void {
	//if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        //return;
    //}

	$base = Plugin\plugin_url();
	$debug = Plugin\debug_enabled();
	$version = $debug ? time() : Plugin\PLUGIN_VERSION;

	\wp_enqueue_script(
		'helsinki-tpr-scripts',
		$debug ? $base . 'assets/admin/js/scripts.js' : $base . 'assets/admin/js/scripts.min.js',
		array(),
		$version,
		true
	);

	\wp_localize_script(
		'helsinki-tpr-scripts',
		'helsinkiTPR',
        array(
			'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
		)
	);

	\wp_set_script_translations(
        'helsinki-tpr-scripts',
        'helsinki-tpr',
        Plugin\plugin_path() . 'languages'
    );

	\wp_enqueue_style(
		'helsinki-tpr-tyles',
		$debug ? $base . 'assets/admin/css/styles.css' : $base . 'assets/admin/css/styles.min.css',
		array(),
		$version,
		'all'
	);
}

\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\public_assets', 1 );
function public_assets(): void {
	$base = Plugin\plugin_url();
	$debug = Plugin\debug_enabled();
	$version = $debug ? time() : Plugin\PLUGIN_VERSION;

	\wp_enqueue_script(
		'helsinki-tpr-scripts',
		$debug ? $base . 'assets/public/js/scripts.js' : $base . 'assets/public/js/scripts.min.js',
		array(),
		$version,
		true
	);

	\wp_localize_script(
		'helsinki-tpr-scripts',
		'helsinkiTPR',
        array(
			'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
		)
	);

	\wp_enqueue_style(
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
function determine_unit( array $attributes ): ?Unit {
	$post_id = ! empty( $attributes['postID'] )
		? \absint( $attributes['postID'] )
		: 0;

	return \apply_filters( 'helsinki_tpr_unit_entity_by_id', null, $post_id );
}

function get_current_language(): string {
	return \apply_filters(
		'helsinki_tpr_current_language',
		substr( \get_locale(), 0, 2 )
	);
}

function render_unit( array $attributes ): string {
	$unit = determine_unit( $attributes );
	if ( ! $unit ) {
		return '';
	}

	$language = get_current_language();

	return sprintf(
		'<div %s class="helsinki-tpr tpr-unit">
			<div class="hds-container">%s%s</div>
		</div>',
		! empty( $attributes['anchor'] )
			? sprintf( 'id="%s"', \esc_attr( $attributes['anchor'] ) )
			: '',
		render_unit_image( $unit, $attributes, $language ),
		render_unit_content( $unit, $attributes, $language )
	);
}

function render_unit_content( Unit $unit, array $attributes, string $language ): string {
	return sprintf(
		'<div class="tpr__container">%s%s</div>',
		render_unit_title( $unit, $attributes, $language ),
		render_unit_data( $unit, $attributes, $language )
	);
}

function render_unit_title( Unit $unit, array $attributes, string $language ): string {
	$title = ! empty( $attributes['unitTitle'] )
		? $attributes['unitTitle']
		: $unit->name( $language );

	return \apply_filters(
		'helsinki_tpr_unit_block_title',
		sprintf(
			'<h2 class="unit__title">%s</h2>',
			\esc_html( $title )
		),
		$title,
		$attributes['postID']
	);
}

function render_unit_data( Unit $unit, array $attributes, string $language ): string {
	return \apply_filters(
		'helsinki_tpr_unit_data',
		implode(
			'',
			determine_unit_data_elements( $unit, $attributes, $language )
		),
		$unit,
		$attributes
	);
}

function determine_unit_data_elements( Unit $unit, array $attributes, string $language ): array {
	$elements = array();

	if ( ! empty( $attributes['showStreetAddress'] ) ) {
		$elements['street_address'] = render_unit_street_address( $unit, $language );
	}

	if ( ! empty( $attributes['showEmail'] ) ) {
		$elements['email'] = render_unit_email( $unit, $language );
	}

	if ( ! empty( $attributes['showPhone'] ) ) {
		$elements['phone'] = render_unit_phone_number( $unit, $language );
	}

	if ( ! empty( $attributes['showOpenHours'] ) ) {
		$elements['open_hours'] = render_unit_open_hours( $unit, $language );
	}

	if ( ! empty( $attributes['showServiceLanguage'] ) ) {
		$elements['service_languages'] = render_unit_service_language( $unit, $language );
	}

	if ( ! empty( $attributes['showWebsite'] ) ) {
		$elements['website'] = render_unit_website( $unit, $language );
	}

	if ( ! empty( $attributes['showPostalAddress'] ) ) {
		$elements['postal_address'] = render_unit_postal_address( $unit, $language );
	}

	if ( ! empty( $attributes['showDirections'] ) ) {
		$elements['directions'] = render_unit_directions( $unit, $language );
	}

	if ( ! empty( $attributes['showAdditionalInfo'] ) ) {
		$elements['additional_info'] = render_unit_additional_info( $unit, $language );
	}

	return \apply_filters(
		'helsinki_tpr_unit_elements',
		array_merge(
			legacy_unit_main_elements( $elements, $unit, $attributes ),
			legacy_unit_secondary_elements( $unit, $attributes )
		),
		$unit,
		$attributes,
		$language
	);
}

function legacy_unit_main_elements( array $elements, Unit $unit, array $attributes ): array {
	return \apply_filters_deprecated(
		'helsinki_tpr_unit_main_elements',
		array( $elements, $unit, $attributes ),
		'2.0.0',
		'helsinki_tpr_unit_elements'
	);
}

function legacy_unit_secondary_elements( Unit $unit, array $attributes ): array {
	return \apply_filters_deprecated(
		'helsinki_tpr_unit_secondary_elements',
		array( array(), $unit, $attributes ),
		'2.0.0',
		'helsinki_tpr_unit_elements'
	);
}

function render_unit_section_title( string $title, string $iconType, string $icon ): string {
	return sprintf(
		'<div class="unit__section_title">
			%s<div>%s:</div>
		</div>',
		render_unit_icon( $iconType, $icon ),
		\esc_html( $title )
	);
}

function render_unit_icon( string $type,  string $icon ): string {
	return \apply_filters( 'hds_wp_svg_icon_html', '', $icon, $type );
}

function render_unit_street_address( Unit $unit, string $language ): string {
	$parts = array();
	if ( ! empty( $unit->street_address( $language ) ) ) {
		if ( ! empty( $unit->address_zip() ) || ! empty( $unit->address_city( $language ) ) ) {
			$parts[] = sprintf('%s,', $unit->street_address( $language ) );
		} else {
			$parts[] = sprintf('%s', $unit->street_address( $language ) );
		}
	}
	if ( ! empty($unit->address_zip() ) ) {
		$parts[] = sprintf( '%s', $unit->address_zip() );
	}
	if ( ! empty( $unit->address_city( $language ) ) ) {
		$parts[] = sprintf( '%s', $unit->address_city( $language ) );
	}

	if ( ! $parts ) {
		return '';
	}

	$route_link = $unit->get_service_map_link( $language );
	if ( $route_link ) {
		$route_link = link_with_screen_reader_text(
			$route_link,
			__( 'View location on service map', 'helsinki-tpr' ),
			$unit->name( $language )
		);
	}

	return sprintf(
		'<div class="unit__street_address">
			%s
			<div class="unit__section_data">
				<div class="address">%s</div>
				%s
			</div>
		</div>',
		render_unit_section_title(
			__('Street address', 'helsinki-tpr'),
			'blocks',
			'location'
		),
		\esc_html( implode( ' ', $parts ) ),
		$route_link
	);
}

function render_unit_directions( Unit $unit, string $language ): string {
	$link = $unit->get_hsl_route_link( $language );

	return $link ? sprintf(
		'<div class="unit__directions">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title(
			__('How to get here', 'helsinki-tpr'),
			'blocks',
			'map'
		),
		link_with_screen_reader_text(
			$link,
			__( 'Show route in the HSL Journey Planner', 'helsinki-tpr' ),
			$unit->name( $language )
		)
	) : '';
}

function render_unit_postal_address( Unit $unit, string $language ): string {
	$address = $unit->postal_address( $language );

	return $address ? sprintf(
		'<div class="unit__postal_address">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title(
			__('Postal address', 'helsinki-tpr'),
			'blocks',
			'location'
		),
		\esc_html( $address ),
	) : '';
}

function render_unit_phone_number( Unit $unit, string $language ): string {
	$phone = $unit->phone();

	return $phone ? sprintf(
		'<div class="unit__phone">
			%1$s
			<div class="unit__section_data">
				<a href="tel:%2$s">%3$s</a>
			</div>
		</div>',
		render_unit_section_title(
			__('Phonenumber', 'helsinki-tpr'),
			'blocks',
			'phone'
		),
		\esc_attr( str_replace( ' ', '', $phone ) ),
		\esc_html( $phone )
	) : '';
}

function render_unit_email( Unit $unit, string $language ): string {
	$email = $unit->email();

	return $email ? sprintf(
		'<div class="unit__email">
			%s
			<div class="unit__section_data">
				<a href="mailto:%s">%s</a>
			</div>
		</div>',
		render_unit_section_title(
			__('Email', 'helsinki-tpr'),
			'blocks',
			'envelope'
		),
		\esc_attr( $email ),
		\esc_html( $email )
	) : '';
}

function render_unit_open_hours( Unit $unit, string $language ): string {
	return $unit->open_hours() ? sprintf(
		'<div class="unit__open_hours">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title( __('Open hours', 'helsinki-tpr'), 'blocks', 'clock' ),
		implode( '', $unit->open_hours_html( $language ) )
	) : '';
}

function render_unit_service_language( Unit $unit, string $language ): string {
	$languages = $unit->available_languages();

	return $languages ? sprintf(
		'<div class="unit__service_language">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title(
			__( 'Service language', 'helsinki-tpr' ),
			'blocks',
			'globe'
		),
		\esc_html( implode( ', ', $languages ) ),
	) : '';
}

function render_unit_website( Unit $unit, string $language ): string {
	$weburl = $unit->website_url( $language );

	return $weburl ? sprintf(
		'<div class="unit__website">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title(
			__( 'Website', 'helsinki-tpr' ),
			'blocks',
			'arrow-right'
		),
		link_with_screen_reader_text(
			$weburl,
			__( 'Go to the website', 'helsinki-tpr' ),
			$unit->name( $language )
		)
	) : '';
}

function render_unit_image( Unit $unit, array $attributes, string $language ): string {
	if ( empty( $attributes['showPhoto'] ) ) {
		return '';
	}

	$image = $unit->html_img( $language );

	return $image ? sprintf(
		'<div class="unit__image">%s</div>',
		$image,
	) : '';
}

function render_unit_additional_info( Unit $unit, string $language ): string {
	return $unit->additional_info() ? sprintf(
		'<div class="unit__additional_info">
			%s
			<div class="unit__section_data">
				%s
			</div>
		</div>',
		render_unit_section_title(
			__('Additional information', 'helsinki-tpr'),
			'blocks',
			'info-circle'
		),
		implode( '', $unit->additional_info_html( $language ) )
	) : '';
}

function link_with_screen_reader_text( string $url, string $anchor, string $hidden_text ): string {
	return sprintf(
		'<a href="%s">
			<span class="screen-reader-text">%s: </span>%s
		</a>',
		\esc_url( $url ),
		\esc_html( $hidden_text ),
		\esc_html( $anchor )
	);
}
