<?php

namespace CityOfHelsinki\WordPress\TPR\Cpt;

use CityOfHelsinki\WordPress\TPR as Plugin;

use CityOfHelsinki\WordPress\TPR\CacheManager;
use CityOfHelsinki\WordPress\TPR\Api\Units;

add_action( 'init', __NAMESPACE__ . '\\register' );
add_action('admin_menu', __NAMESPACE__ . '\\menu' );

function register() {
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

function menu() {
	add_submenu_page(
        'edit.php?post_type=helsinki_tpr_unit',
        __( 'Add new unit', 'helsinki-tpr' ),
        __( 'Add new unit', 'helsinki-tpr' ),
        'manage_options',
        'add-new-tpr-unit',
        __NAMESPACE__ . '\\render_search',
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

function render_search() {
	require_once Plugin\views_path( 'unit' ) . 'unit-search.php';
}

function render_metabox( $post, $metabox ) {
	$savedOptions = maybe_unserialize( $post->post_content );

	wp_nonce_field( 'helsinki-tpr-unit-nonce', 'helsinki-tpr-unit-nonce' );

	$unit = Units::entities($post->ID);
	Plugin\metabox_view( 'unit-data', $unit );
}

function render_unit_data_row($name, array $values, $classes = '') {
	$html = '';
	$classes_html = '';
	if ($classes) {
		$classes_html = sprintf('class="%s"', $classes);
	}
	foreach ($values as $value) {
		if ($value) {
			$html .= sprintf('<div %s>%s</div>', $classes_html, $value);
		}
	}
	if (empty($html)) {
		$html = sprintf('<div>%s</div>', __('(empty)', 'helsinki-tpr'));
	}
	printf(
		'<div class="row">
			<h3>%s</h3>
			%s
		</div>',
		$name,
		$html
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
