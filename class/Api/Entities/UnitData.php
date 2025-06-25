<?php

namespace CityOfHelsinki\WordPress\TPR\Api\Entities;

use stdClass;

class UnitData
{
	private array $data;

    public function __construct( array $data )
    {
		$this->data = $data;
    }

	public function to_array(): array
	{
		return $this->data;
	}

	public function get_data( string $key, $default = null )
	{
		return array_key_exists( $key, $this->data )
			? $this->data[$key]
			: $default;
	}

	public function get_translated_data( string $key, string $language = '', $default = null )
	{
		if ( ! array_key_exists( $key, $this->data ) ) {
			return $default;
		}

		if ( ! array_key_exists( $language, $this->data[$key] ) ) {
			return $default;
		}

		return $this->data[$key][$language];
	}

    public static function from_response( stdClass $response ): self
    {
		$data = array(
			'helsinki_tpr_version' => \CityOfHelsinki\WordPress\TPR\plugin_version(),
		);

		$simple_details = array(
			'id' => '',
		    'org_id' => '',
		    'dept_id' => '',
		    // 'provider_type' => '',
		    // 'data_source_url' => '',
		    'latitude' => '',
		    'longitude' => '',
		    // 'northing_etrs_gk25' => '',
		    // 'easting_etrs_gk25' => '',
		    // 'northing_etrs_tm35fin' => '',
		    // 'easting_etrs_tm35fin' => '',
		    // 'manual_coordinates' => false,
		    'address_zip' => '',
		    'email' => '',
		    // 'accessibility_viewpoints' => '',
		    // 'accessibility_phone' => '',
		    // 'accessibility_email' => '',
		    // 'accessibility_www' => '',
		    // 'created_time' => '',
		    // 'modified_time' => '',
		    'picture_url' => '',
		    // 'ontologyword_ids' => array(),
			// 'ontologytree_ids' => array(),
		);

		foreach ( $simple_details as $key => $default ) {
			$data[$key] = $response->$key ?? $default;
		}

		$complex_details = array(
		    // 'ontologyword_details' => array(),
		    // 'sources' => array(),
		    'connections' => function( stdClass $response ): array {
				if ( ! empty( $response->connections ) ) {
					return array_map(
						fn( $data ) => (array) $data,
						(array) $response->connections
					);
				}

				return array();
			},
		    'service_descriptions' => function( stdClass $response ): array {
				if ( ! empty( $response->service_descriptions ) ) {
					return array_map(
						fn( $data ) => (array) $data,
						(array) $response->service_descriptions
					);
				}

				return array();
			},
		    // 'accessibility_sentences' => array(),
		);

		foreach ( $complex_details as $key => $callback ) {
			$data[$key] = call_user_func( $callback, $response );
		}

		$languages = array( 'fi', 'en', 'sv' );
		$translatables = array(
		    'name' => '',
		    'short_desc' => '',
		    'desc' => '',
		    'street_address' => '',
		    'address_city' => '',
		    'address_postal_full' => '',
		    'call_charge_info' => '',
		    'www' => '',
		    'picture_caption' => '',
		);

		foreach ( $translatables as $translatable => $default ) {
			$data[$translatable] = array();

			foreach ( $languages as $language ) {
				$key = $translatable . '_' . $language;

				$data[$translatable][$language] = $response->$key ?? $default;
			}
		}

		return new self( $data );
    }
}
