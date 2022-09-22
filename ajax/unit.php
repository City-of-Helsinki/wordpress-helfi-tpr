<?php

namespace CityOfHelsinki\WordPress\TPR\Ajax;

use CityOfHelsinki\WordPress\TPR as Plugin;
use CityOfHelsinki\WordPress\TPR\Cpt as Config;
use CityOfHelsinki\WordPress\TPR\Blocks as Blocks;
use CityOfHelsinki\WordPress\TPR\Api\Units;

add_action( 'wp_ajax_helsinki_import_tpr_unit', __NAMESPACE__ . '\\import_tpr_unit' );

function import_tpr_unit() {
	$id = $_POST['id'] ?? 0;
	$title = $_POST['title'] ?? '';
	if ( ! $id  ) {
		wp_send_json_error( null, 404 );
	}

	$id = wp_strip_all_tags($id);
	$title = wp_strip_all_tags($title);
	$post_id = wp_insert_post(array(
		'post_title' => $title,
		'post_type' => 'helsinki_tpr_unit',
		'post_status' => 'publish',
		'meta_input' => array(
			'tpr_id' => $id
		)
	));
	if ( ! $post_id  ) {
		wp_send_json_error( null, 404 );
	}
	wp_send_json_success( array(
		'post_id' => $post_id,
		'link_html' => Config\link_to_tpr_edit_post($post_id)
	) );

}
