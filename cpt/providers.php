<?php

namespace CityOfHelsinki\WordPress\TPR\Cpt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\CacheManager;
use CityOfHelsinki\WordPress\TPR\Api\Units;
use CityOfHelsinki\WordPress\TPR\Api\Entities\Unit;
use CityOfHelsinki\WordPress\TPR\Api\Entities\UnitData;

function provide_unit_entity_by_id( $unit, int $id ) {
	$data = \apply_filters( 'helsinki_tpr_unit_cache_data', array(), $id );

	if ( ! $data ) {
		$data = Units::entities( $id );

		if ( $data ) {
			\do_action( 'helsinki_tpr_cache_unit_data', $id, $data, \HOUR_IN_SECONDS );
		}
	}

	return $data
		? \apply_filters( 'helsinki_tpr_unit_entity', new Unit( $data ), $id )
		: $unit;
}

function provide_units_search( array $units, string $query ): array {
	if ( ! $query ) {
		return $units;
	}

	$result = Units::search_units( $query );

	return is_array( $result ) ? array_merge( $units, $result ) : $units;
}

function provide_unit_cache_data( $data, int $unit_post_id ) {
	return CacheManager::load( unit_cache_key( $unit_post_id ) ) ?: $data;
}

function provide_cache_unit_data( int $unit_post_id, UnitData $data, int $expiration = 0 ): void {
	CacheManager::store( unit_cache_key( $unit_post_id ), $data, $expiration );
}

function provide_clear_unit_cache( int $unit_post_id ): void {
	CacheManager::clear( unit_cache_key( $unit_post_id ) );
}

function unit_cache_key( int $unit_post_id ): string {
	return 'unit-' . $unit_post_id;
}
