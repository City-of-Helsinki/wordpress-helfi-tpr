<?php

declare(strict_types = 1);

namespace CityOfHelsinki\WordPress\TPR\Features\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function CityOfHelsinki\WordPress\TPR\plugin_path;
use WP_Block_Editor_Context;
use WP_Block_Type_Registry;

\add_action( 'helsinki_tpr_init', __NAMESPACE__ . '\\init' );
function init(): void {
	\add_action(
		'init',
		__NAMESPACE__ . '\\register_blocks'
	);

	\add_action(
		'block_categories_all',
		__NAMESPACE__ . '\\register_categories', 10, 2
	);

	\add_filter(
		'helsinki_wp_allowed_blocks',
		__NAMESPACE__ . '\\provide_allowed_blocks'
	);

	\add_filter(
		'helsinki_tpr_current_language',
		__NAMESPACE__ . '\\determine_current_language'
	);

	\add_filter(
		'load_script_translation_file',
		__NAMESPACE__ . '\\translations_location',
		10, 3
	);

	\add_filter(
		'allowed_block_types_all',
		__NAMESPACE__ . '\\block_post_types',
		10, 2
	);
}

function register_blocks(): void {
	$path = \plugin_dir_path( __FILE__ );

	require_once $path . 'unit/render.php';

	\register_block_type(
		$path . 'unit/block.json',
		array( 'render_callback' => __NAMESPACE__ . '\\Unit\render' )
	);
}

function register_categories( array $categories, $editor_context ): array {
	if ( $editor_context instanceof WP_Block_Editor_Context ) {
		return array_merge( $categories, array(
			array(
				'slug' => 'helsinki-tpr',
				'title' => __( 'Helsinki', 'helsinki-tpr' ),
				'icon'  => 'building',
			),
		) );
	}

	return $categories;
}

function block_post_types( bool|array $allowed_block_types, WP_Block_Editor_Context $context ): bool|array {
	if ( 'post' === $context?->post->post_type ) {
		if ( ! is_array( $allowed_block_types ) ) {
			$allowed_block_types = array_keys(
				WP_Block_Type_Registry::get_instance()->get_all_registered()
			);
		}

		$allowed_block_types = array_flip( $allowed_block_types );

		if ( isset( $allowed_block_types['helsinki-tpr/unit'] ) ) {
			unset( $allowed_block_types['helsinki-tpr/unit'] );
		}

		return array_flip( $allowed_block_types );
	}

	return $allowed_block_types;
}

function provide_allowed_blocks( array $blocks ): array {
	if ( isset( $blocks['post_types']['page'] ) ) {
		$blocks['post_types']['page']['helsinki-tpr/unit'] = true;
	}

	return $blocks;
}

function determine_current_language( string $language ): string {
	return array_reduce(
		array( 'pll_default_language', 'pll_current_language' ),
		function( string $current, $handler ) {
			if ( function_exists( $handler ) ) {
				$current = call_user_func( $handler ) ?: $current;
			}

			return $current;
		},
		$language
	);
}

function translations_location( string $file, string $handle, string $domain ): string {
	if ( 'helsinki-tpr' === $domain ) {
		return str_replace( WP_LANG_DIR . '/plugins', plugin_path() . 'languages', $file );
	}

	return $file;
}
