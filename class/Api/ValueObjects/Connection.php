<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\ConnectionTypeInterface;

class Connection
{
	protected ConnectionTypeInterface $type;
	protected array $data;

	public function __construct( array $data )
	{
		$this->setup_data();
		$this->populate_data( $data );

		$this->setup_type( $data );
	}

	public function type(): string
	{
		return $this->type->name();
	}

	public function name( string $lang ): string
	{
		return $this->get_data( 'name', $lang, '' );
	}

	public function url( string $lang ): string
	{
		return $this->get_data( 'www', $lang, '' );
	}

	public function is_opening_hour(): bool
	{
		return $this->type->is_opening_hour();
	}

	public function is_link(): bool
	{
		return $this->type->is_link();
	}

	public function is_eservice_link(): bool
	{
		return $this->type->is_eservice_link();
	}

	public function to_html( string $language ): string
	{
		return $this->type->to_html( $this, $language );
	}

	protected function get_data( string $key, string $lang, $default = null )
	{
		return $this->has_data( $key, $lang ) ? $this->data[$key][$lang] : $default;
	}

	protected function has_data( string $key, string $lang ): bool
	{
		return $lang && isset( $this->data[$key][$lang] );
	}

	protected function setup_type( array $data ): void
	{
		$type = $this->determine_type_class( $data['section_type'] ?? '' );

		$this->type = new $type();
	}

	protected function determine_type_class( string $type ): string
	{
		$types = array(
			'ESERVICE_LINK' => \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\EServiceLinkConnection::class,
			'HIGHLIGHT' => \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\HighlightConnection::class,
			'LINK' => \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\LinkConnection::class,
			'SOCIAL_MEDIA_LINK' => \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\SocialMediaLinkConnection::class,
			'OPENING_HOURS' => \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\OpeningHourConnection::class,
		);

		if ( 'OPENING_HOURS' === $type && ! empty( $this->data['www'] ) ) {
			return \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\OpeningHourLinkConnection::class;
		}

		return $types[$type] ?? \CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes\UnknownConnection::class;
	}

	protected function setup_data(): void
	{
		$this->data = array(
			'name' => array(),
			'www' => array(),
		);
	}

	protected function populate_data( array $data ): void
	{
		array_walk( $data, array( $this, 'process_data_item' ) );
	}

	protected function process_data_item( $value, $key ): void
	{
		$parts = $this->data_key_parts( $key );

		if ( $this->is_valid_key( $parts ) ) {
			$this->data[$parts[0]][$parts[1]] = $value;
		}
	}

	protected function data_key_parts( $key ): array
	{
		return ( $key && is_string( $key ) ) ? explode( '_', $key ) : array();
	}

	protected function is_valid_key( array $parts ): bool
	{
		return ! empty( $parts[0] )
			&& ! empty( $parts[1] )
			&& array_key_exists( $parts[0], $this->data )
			&& ! array_key_exists( $parts[1], $this->data[$parts[0]] );
	}
}
