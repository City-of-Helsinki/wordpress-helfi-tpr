<?php
/**
 * Unit entity
 *
 * @link: https://api.hel.fi/linkedevents/v1/event/helsinki:afxsfaqz44/?include=keywords,location
 * @link: https://dev.hel.fi/apis/service-map-backend-api#documentation
 */

namespace CityOfHelsinki\WordPress\TPR\Api\Entities;

use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Connection;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Language;

class Unit
{
    private UnitData $data;
    private array $settings;
	private array $connections;
	private array $languages;

    public function __construct( UnitData $data, array $settings = [] ) {
        $this->data = $data;
        $this->settings = $settings;

		$this->setup_connections();
		$this->setup_languages();
    }

	private function setup_connections(): void
	{
		$this->connections = array();

		foreach ( $this->data->get_data( 'connections', array() ) as $data ) {
			$this->add_connection($data );
		}
	}

	private function add_connection( $data ): void
	{
		$connection = new Connection( (array) $data );

		if ( ! isset( $this->connections[$connection->type()] ) ) {
			$this->connections[$connection->type()] = array();
		}

		$this->connections[$connection->type()][] = $connection;
	}

	private function setup_languages(): void
	{
		$this->languages = array();

		array_reduce(
			$this->data->get_data( 'provided_languages', array() ),
			fn( $carry, $code ) => $this->add_language( $code )
		);
	}

	private function add_language( string $code ): void
	{
		if ( ! isset( $this->languages[$code] ) ) {
			$this->languages[$code] = Language::fromCode( $code );
		}
	}

    public function id(): string
	{
		return (string) $this->data->get_data( __FUNCTION__, '' );
    }

    public function name( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( __FUNCTION__, $language, '' );
    }

    public function short_description( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( 'short_desc', $language, '' );
    }

    public function description( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( __FUNCTION__, $language, '' );
    }

    public function phone(): string
	{
		return $this->data->get_data( __FUNCTION__, '' );
    }

    public function email(): string
	{
        return $this->data->get_data( __FUNCTION__, '' );
    }

    public function website_url( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( 'www', $language, '' );
    }

    public function street_address( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( __FUNCTION__, $language, '' );
    }

    public function address_zip(): string
	{
        return $this->data->get_data( __FUNCTION__, '' );
    }

    public function address_city( string $language = 'en' ): string
	{
        return $this->data->get_translated_data( __FUNCTION__, $language, '' );
    }

    public function postal_address( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( 'address_postal_full', $language, '' );
    }

    public function open_hours( string $deprecated = null ): array
	{
        return $this->connections['OPENING_HOURS'] ?? array();
    }

	public function open_hours_html( string $language ): array
	{
		return array_map(
			function ( Connection $connection ) use ( $language ) {
				return $connection->to_html( $language );
			},
			$this->open_hours()
		);
	}

    public function available_languages(): array
	{
		return array_map(
			fn( $language ) => $language->label(),
			$this->languages
		);
    }

    public function additional_info( string $language = 'en' ): ?array
	{
		return array_map(
			function ( Connection $connection ) use ( $language ) {
				return $connection->name( $language );
			},
			$this->connections['HIGHLIGHT'] ?? array()
		);
    }

	protected static function get_property( $item, $property )
	{
		if ( is_object( $item ) ) {
			return property_exists( $item, $property ) ? (array) $item->{$property} : [];
		} else {
			return $item[$property] ?? [];
		}
	}

    public function get_service_map_link( string $language = 'en' ): string
	{
		if ( ! $this->id() ) {
			return '';
		}

		if ( ! in_array( $language, array( 'fi', 'en', 'sv', 'svg' ) ) ) {
			$language = 'en';
		}

		return sprintf(
			'https://palvelukartta.hel.fi/%s/unit/%s',
			$language,
			$this->id()
		);
    }

    public function get_hsl_route_link( string $language = 'en' ): string
	{
		if ( ! $this->has_route_data() ) {
			return '';
		}

		if ( ! in_array( $language, array( 'fi', 'en', 'sv' ) ) ) {
			$language = 'en';
		}

        return sprintf(
			'https://reittiopas.hsl.fi/%s/reitti/POS/',
            $language,
        ) . rawurlencode( sprintf(
			'%s, %s::%s,%s',
            $this->street_address(),
            $this->address_city(),
            $this->get_latitude(),
            $this->get_longitude(),
        ) );
    }

	private function has_route_data(): bool
	{
		return $this->street_address()
			&& $this->address_city()
			&& $this->get_latitude()
			&& $this->get_longitude();
	}

    public function get_latitude(): ?float
	{
		return $this->data->get_data( 'latitude', null );
    }

    public function get_longitude(): ?float
	{
		return $this->data->get_data( 'longitude', null );
    }

    public function image_url(): string
	{
        return $this->data->get_data( 'picture_url', '' );
    }

    public function image_alt_text( string $language = 'en' ): string
	{
		return $this->data->get_translated_data( 'picture_caption', $language, '' );
    }

	public function html_img( string $language = 'en' ): string
	{
		return $this->image_url()
			? sprintf(
				'<img src="%s" alt="%s">',
				esc_url( $this->image_url() ),
				esc_attr( $this->image_alt_text($language) )
			)
			: '';
	}
}
