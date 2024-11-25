<?php
/**
 * Unit entity
 *
 * @link: https://api.hel.fi/linkedevents/v1/event/helsinki:afxsfaqz44/?include=keywords,location
 * @link: https://dev.hel.fi/apis/service-map-backend-api#documentation
 */

namespace CityOfHelsinki\WordPress\TPR\Api\Entities;

use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Connection;
use DateTime;
use Exception;

/**
 * Class Event
 */
class Unit extends Entity
{
    private array $settings;
	private array $connections;

    public function __construct( $entity_data, array $settings = [] ) {
        $this->settings = $settings;

        parent::__construct( $entity_data );

		$this->setup_connections();
    }

	private function setup_connections(): void
	{
		$this->connections = array();

		if ( ! empty( $this->entity_data->connections ) ) {
			$this->add_connections( (array) $this->entity_data->connections );
		}
	}

	private function add_connections( array $data ): void
	{
		array_walk( $data, array( $this, 'add_connection' ) );
	}

	private function add_connection( $data ): void
	{
		$connection = new Connection( (array) $data );

		if ( ! isset( $this->connections[$connection->type()] ) ) {
			$this->connections[$connection->type()] = array();
		}

		$this->connections[$connection->type()][] = $connection;
	}

    public function id(): string
	{
        return (string) $this->entity_data->id;
    }

    public function name( string $language = null ): ?string
	{
        return $this->key_by_language( 'name', null, $language );
    }

    public function short_description( string $language = null ): ?string
	{
        return $this->key_by_language( 'short_description', null, $language );
    }

    public function description( string $language = null ): ?string
	{
        return $this->key_by_language( 'description', null, $language );
    }

    public function phone(): ?string
	{
        return $this->entity_data->phone ?? null;
    }

    public function email(): ?string
	{
        return $this->entity_data->email ?? null;
    }

    public function website_url( string $language = null ): ?string
	{
        return $this->key_by_language( 'www', null, $language );
    }

    public function street_address( string $language = null ): ?string
	{
        return $this->key_by_language( 'street_address', null, $language );
    }

    public function address_zip(): ?string
	{
        return $this->entity_data->address_zip ?? null;
    }

    public function address_city( string $language = null ): ?string
	{
        return $this->key_by_language( 'address_city', null, $language );
    }

    public function postal_address( string $language = null ): ?string
	{
        return $this->key_by_language( 'address_postal_full', null, $language );
    }

    public function open_hours( string $language = null ): ?array
	{
        return $this->connections['OPENING_HOURS'] ?? array();
    }

    public function additional_info( string $language = null ): ?array
	{
        $connections = $this->entity_data->connections ?? null;
        $additional_info = array();
        if ($connections) {
            foreach ($connections as $section) {
                if ($this->get_property($section, 'section_type') == 'HIGHLIGHT') {
                    $additional_info[] = $this->key_by_language( 'name', $section, $language );
                }
            }
        }
        return $additional_info;
    }

	protected static function get_property( $item, $property )
	{
		if ( is_object( $item ) ) {
			return property_exists( $item, $property ) ? (array) $item->{$property} : [];
		} else {
			return $item[$property] ?? [];
		}
	}

    public function get_service_map_link(): ?string
	{
        $allowed_langs = array(
            'fi',
            'en',
            'svg'
        );
        $current_lang = $this->current_language();
        if (!in_array($current_lang, $allowed_langs)) {
            $current_lang = 'en';
        }

        if (!$this->id()) {
            return null;
        }

        return 'https://palvelukartta.hel.fi/' . $current_lang . '/unit/' . $this->id();
    }

    public function get_hsl_route_link(): ?string
	{
        $allowed_langs = array(
            'fi',
            'en',
            'sv'
        );
        $current_lang = $this->current_language();
        if (!in_array($current_lang, $allowed_langs)) {
            $current_lang = 'en';
        }

        $street_address = $this->street_address();
        $address_city = $this->address_city();
        $latitude = $this->get_latitude();
        $longitude = $this->get_longitude();

        if (!$street_address || !$address_city || !$latitude || !$longitude) {
            return null;
        }

        return sprintf('https://reittiopas.hsl.fi/%s/reitti/POS/',
            $current_lang,
        ) . rawurlencode(sprintf('%s, %s::%s,%s',
            $this->street_address(),
            $this->address_city(),
            $this->get_latitude(),
            $this->get_longitude(),
        ));
    }

    public function get_latitude(): ?float
	{
        return $this->entity_data->latitude ?? null;
    }

    public function get_longitude(): ?float
	{
        return $this->entity_data->longitude ?? null;
    }

    public function image_url(): ?string
	{
        return $this->entity_data->picture_url ?? null;
    }

    public function image_alt_text( string $language = null ): ?string
	{
        return $this->key_by_language( 'picture_caption', null, $language );
    }

	public function html_img( string $language = null ): string
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
