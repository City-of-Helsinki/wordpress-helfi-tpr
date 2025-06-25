<?php

namespace CityOfHelsinki\WordPress\TPR;

use CityOfHelsinki\WordPress\TPR\Api\Entities\UnitData;

abstract class CacheManager
{
	public static function clear( string $key ): void
	{
		$cache = self::cache_file( $key );
		if ( file_exists( $cache ) ) {
			@unlink( $cache );
		}
		\delete_transient( 'helsinki_tpr_' . $key );
	}

	public static function load( string $key ): ?UnitData
	{
		$file = \get_transient( 'helsinki_tpr_cache_file_' . $key );
		if ( ! $file ) {
			return null;
		}

		$cache = self::cache_file( $key );
		if ( ! file_exists( $cache ) ) {
			return null;
		}

		$cache = file_get_contents( $cache ) ?: '';

		$json = json_decode( $cache, true );
		if ( json_last_error() ) {
			return null;
		}

		if ( $json instanceof \stdClass ) {
			return UnitData::from_response( $json );
		}

		return new UnitData( $json );
	}

	public static function store( string $key, UnitData $data, int $expiration = 0 ): bool
	{
		if ( ! $key || ! $data->to_array() ) {
			return false;
		}

		$data = json_encode( $data->to_array() );
		if ( ! $data ) {
			return false;
		}

		$cache = self::cache_file( $key );
		if ( false === file_put_contents( $cache, $data ) ) {
			return false;
		}

		return \set_transient(
			'helsinki_tpr_cache_file_' . $key,
			$cache,
			$expiration ? $expiration : HOUR_IN_SECONDS
		);
	}

	public static function cache_file( $key ): string
	{
		return \trailingslashit( self::cache_dir() ) . "cache-{$key}.json";
	}

	public static function cache_dir(): string
	{
		$path = \trailingslashit( wp_upload_dir()['basedir'] ) . 'helsinki-tpr';

		if ( ! file_exists( $path ) ) {
			\wp_mkdir_p( $path );
			file_put_contents( \trailingslashit( $path ) . 'index.php', '<?php' );
		}

		return $path;
	}

	public static function delete_cache_dir(): void
	{
		$path = self::cache_dir();

		if ( file_exists( $path ) ) {
			rmdir( $path );
		}
	}
}
