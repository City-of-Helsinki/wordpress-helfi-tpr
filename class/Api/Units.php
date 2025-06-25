<?php

namespace CityOfHelsinki\WordPress\TPR\Api;

use CityOfHelsinki\WordPress\TPR\CacheManager;
use CityOfHelsinki\WordPress\TPR\Api\Entities\UnitData;

class Units extends Client {

	public static function entities( int $config_post_id ): ?UnitData
	{
		$unit_id = self::unit_id( $config_post_id );
		if ( ! $unit_id ) {
			return null;
		}

		$response = self::get( 'unit/' . $unit_id );

		return $response
			? UnitData::from_response( $response )
			: null;
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

	protected static function unit_id( int $post_id ): string
	{
		return \get_post_meta( $post_id, 'tpr_id', true ) ?: '';
	}

	public static function search_units(string $query) {
		$response = self::get( 'unit', array_filter( array( 'search' => $query ) ) );
		return $response;
	}

}
