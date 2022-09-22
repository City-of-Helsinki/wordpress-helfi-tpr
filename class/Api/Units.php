<?php

namespace CityOfHelsinki\WordPress\TPR\Api;

use CityOfHelsinki\WordPress\TPR\CacheManager;
use CityOfHelsinki\WordPress\TPR\Api\Entities\Unit;
use CityOfHelsinki\WordPress\TPR\Api\Filters\Places;

class Units extends Client {

	public static function entities( int $config_post_id ) {
		$item = CacheManager::load( 'unit-' . $config_post_id );
		if ( $item ) {
			return new Unit($item);
		}

		$unit_id = self::unit_id( $config_post_id );

		$response = self::get( 'unit/' . $unit_id );
		if ( empty( $response ) ) {
			return array();
		}

		CacheManager::store(
			'unit-' . $config_post_id,
			$response
		);

		return new Unit($response);
	}

	public static function current_language_entities( int $config_post_id  ) {
		$units = self::entities( $config_post_id );
		if ( ! $units ) {
			return array();
		}

		return array_filter( $units, function( $event ){
			return ! empty( $event->name() );
		} );
	}

	protected static function unit_id( int $post_id ) {
		$tpr_id = get_post_meta($post_id, 'tpr_id', true);

		return $tpr_id;
	}

	public static function search_units(string $query) {
		$response = self::get( 'unit', array_filter( array( 'search' => $query ) ) );
		return $response;
	}

}
