<?php

namespace CityOfHelsinki\WordPress\TPR\Cpt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR as Plugin;
use CityOfHelsinki\WordPress\TPR\CacheManager;
use CityOfHelsinki\WordPress\TPR\Api\Units;

\add_action( 'helsinki_tpr_init', __NAMESPACE__ . '\\setup_feature' );
function setup_feature(): void {
	\add_action( 'init', __NAMESPACE__ . '\\register' );

	\add_filter( 'helsinki_tpr_unit_cache_data', __NAMESPACE__ . '\\provide_unit_cache_data', 10, 2 );
	\add_action( 'helsinki_tpr_cache_unit_data', __NAMESPACE__ . '\\provide_cache_unit_data', 10, 3 );
	\add_action( 'helsinki_tpr_clear_unit_cache', __NAMESPACE__ . '\\provide_clear_unit_cache', 10, 1 );

	\add_filter( 'helsinki_tpr_unit_entity_by_id', __NAMESPACE__ . '\\provide_unit_entity_by_id', 10, 2 );
	\add_filter( 'helsinki_tpr_units_search', __NAMESPACE__ . '\\provide_units_search', 10, 2 );

	\add_action( 'admin_menu', __NAMESPACE__ . '\\menu' );
	\add_action( 'helsinki_tpr_unit_menu_page', __NAMESPACE__ . '\\render_unit_menu_page_search', 10 );
	\add_action( 'helsinki_tpr_unit_menu_page', __NAMESPACE__ . '\\render_unit_menu_page_list', 20 );
}

function register(): void {
    register_post_type(
		'helsinki_tpr_unit',
		array(
	        'labels'             => array(
		        'name'                  => __( 'TPR Units', 'helsinki-tpr' ),
		        'singular_name'         => __( 'TPR Unit', 'helsinki-tpr' ),
				'menu_name'             => __( 'Units (TPR)', 'helsinki-tpr' ),
				'all_items'				=> __( 'Units', 'helsinki-tpr' ),
		    ),
	        'public'             => false,
	        'publicly_queryable' => false,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
			'capabilities'		 => array(
				'create_posts'	 => false,
			),
			'map_meta_cap'		 => true,
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'show_in_rest'       => true,
	        'supports'           => array( 'title' ),
			'delete_with_user' => false,
			'can_export' => true,
	    )
	);
}

function menu(): void {
	require_once \plugin_dir_path( __FILE__ ) . 'class-helsinki-tpr-search-table.php';

	\add_submenu_page(
        'edit.php?post_type=helsinki_tpr_unit',
        __( 'Add new unit', 'helsinki-tpr' ),
        __( 'Add new unit', 'helsinki-tpr' ),
        'manage_options',
        'add-new-tpr-unit',
        __NAMESPACE__ . '\\render_unit_menu_page',
		100
    );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_assets' );
function admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
}

add_filter( 'use_block_editor_for_post_type', __NAMESPACE__ . '\\disable_editor', 10, 2 );
function disable_editor( $current_status, $post_type ) {
	if ( 'helsinki_tpr_unit' === $post_type ) {
		return false;
	}
    return $current_status;
}

add_action( 'add_meta_boxes_helsinki_tpr_unit', __NAMESPACE__ . '\\metabox' );
function metabox( $post ) {
	add_meta_box(
        'helsinki-tpr-unit',
        __( 'TPR Unit', 'helsinki-tpr' ),
        __NAMESPACE__ . '\\render_metabox',
        'helsinki_tpr_unit',
        'advanced',
        'high'
    );
}

function render_unit_menu_page(): void {
	require_once Plugin\views_path( 'unit' ) . 'unit-search.php';
}

function render_unit_menu_page_search(): void {
	require_once Plugin\views_path( 'unit' ) . 'unit-config.php';
}

function render_unit_menu_page_list(): void {
	$searchTable = new Helsinki_TPR_Search_Table();
	$searchTable->prepare_items();
	$searchTable->display();
}

function render_metabox( $post, $metabox ): void {
	$savedOptions = maybe_unserialize( $post->post_content );

	wp_nonce_field( 'helsinki-tpr-unit-nonce', 'helsinki-tpr-unit-nonce' );

	Plugin\metabox_view( 'unit-data', array(
		'unit' => \apply_filters(
			'helsinki_tpr_unit_entity_by_id',
			null,
			$post->ID
		),
		'active_language' => 'fi',
		'languages' => array(
			'fi' => array(
				'name' => 'finnish',
				'label' => __( 'Finnish', 'helsinki-tpr' ),
			),
			'en' => array(
				'name' => 'english',
				'label' => __( 'English', 'helsinki-tpr' ),
			),
			'sv' => array(
				'name' => 'swedish',
				'label' => __( 'Swedish', 'helsinki-tpr' ),
			),
		),
	) );
}

function render_unit_data_row( string $name, array $values, $classes = '' ) {
	$classes_html = $classes
		? sprintf( 'class="%s"', \esc_attr( $classes ) )
		: '';

	$html = array_reduce(
		$values,
		function( $carry, $value ) use ( $classes_html ) {
			if ( $value ) {
				$carry .= sprintf(
					'<div %s>%s</div>',
					$classes_html,
					$value
				);
			}

			return $carry;
		},
		''
	);

	if ( empty( $html ) ) {
		$html = sprintf(
			'<div>%s</div>',
			__( '(empty)', 'helsinki-tpr' )
		);
	}

	printf(
		'<div class="row">
			<h3>%s</h3>
			%s
		</div>',
		\esc_html( $name ),
		\wp_kses_post( $html )
	);
}

function link_to_tpr_edit_post($post_id) {
	return sprintf(
		'<a href="%s" class="button button-primary">%s</a>',
		get_admin_url(null, 'post.php?post=' . $post_id. '&action=edit'),
		__('Open post', 'helsinki-tpr')
	);
}

add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\\filter_post_data' , 99, 2 );
function filter_post_data( $data , $postarr ) {
	if (
		empty( $_POST['helsinki-unit-nonce'] ) ||
		! wp_verify_nonce( $_POST['helsinki-unit-nonce'], 'helsinki-unit-nonce' )
	) {
		return $data;
	}

	if (
		empty( $postarr['ID'] ) ||
		! current_user_can( 'edit_post', $postarr['ID'] )
	) {
		return $data;
	}

	$config = $_POST['unit_config'] ?? array();
	if ( $config ) {
		$data['post_content'] = maybe_serialize( $config );
		CacheManager::clear( 'unit-' . $postarr['ID'] );
	}

    return $data;
}

add_action( 'delete_post', __NAMESPACE__ . '\\delete_tpr_unit_config' , 10, 2  );
function delete_tpr_unit_config( $postid, $post ) {
	if ( 'helsinki_tpr_unit' === $post->post_type ) {
		CacheManager::clear( 'unit-' . $postid );
	}
}
