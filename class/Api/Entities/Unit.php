<?php
/**
 * Unit entity
 *
 * @link: https://api.hel.fi/linkedevents/v1/event/helsinki:afxsfaqz44/?include=keywords,location
 * @link: https://dev.hel.fi/apis/service-map-backend-api#documentation
 */

namespace CityOfHelsinki\WordPress\TPR\Api\Entities;

use DateTime;
use Exception;

/**
 * Class Event
 */
class Unit extends Entity {

    /**
     * Event settings
     *
     * @var array
     */
    private array $settings;

    /**
     * Event constructor.
     *
     * @param mixed $entity_data Entity data.
     * @param array $settings    Event settings.
     */
    public function __construct( $entity_data, array $settings = [] ) {
        $this->settings = $settings;

        parent::__construct( $entity_data );
    }

    /**
     * Get Id
     *
     * @return mixed
     */
    public function id() {
        return $this->entity_data->id;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function name($language = null) {
        return $this->key_by_language( 'name', null, $language);
    }

    /**
     * Get short description
     *
     * @return string|null
     */
    public function short_description($language = null) {
        return $this->key_by_language( 'short_description', null, $language );
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function description($language = null) {
        return $this->key_by_language( 'description', null, $language );
    }

    /**
     * Get phone
     *
     * @return string|null
     */
    public function phone() {
        return $this->entity_data->phone ?? null;
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function email() {
        return $this->entity_data->email ?? null;
    }

    /**
     * Get website url
     *
     * @return string|null
     */
    public function website_url($language = null) {
        return $this->key_by_language( 'www', null, $language );
    }

    /**
     * Get street address
     *
     * @return string|null
     */
    public function street_address($language = null) {
        return $this->key_by_language( 'street_address', null, $language );
    }

    /**
     * Get address zip
     *
     * @return string|null
     */
    public function address_zip() {
        return $this->entity_data->address_zip ?? null;
    }

    /**
     * Get address city
     *
     * @return string|null
     */
    public function address_city($language = null) {
        return $this->key_by_language( 'address_city', null, $language );
    }

    /**
     * Get postal address
     *
     * @return string|null
     */
    public function postal_address($language = null) {
        return $this->key_by_language( 'address_postal_full', null, $language );
    }

    /**
     * Get open hours
     *
     * @return array|null
     */
    public function open_hours($language = null) {
        $connections = $this->entity_data->connections ?? null;
        $open_hours = array();
        if ($connections) {
            foreach ($connections as $section) {
                if ($this->get_property($section, 'section_type') == 'OPENING_HOURS') {
                    $open_hours[] = $this->key_by_language( 'name', $section, $language );
                }
            }
        }
        return $open_hours;
    }

    /**
     * Get additional info
     *
     * @return array|null
     */
    public function additional_info($language = null) {
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

	protected static function get_property( $item, $property ) {
		if ( is_object( $item ) ) {
			return property_exists( $item, $property ) ? (array) $item->{$property} : [];
		} else {
			return $item[$property] ?? [];
		}
	}


    /**
     * Get image url
     *
     * @return string|null
     */
    public function image_url() {
        return $this->entity_data->picture_url ?? null;
    }

    /**
     * Get image alt text
     *
     * @return string
     */
    public function image_alt_text($language = null) {
        return $this->key_by_language( 'picture_caption', null, $language );
    }

	/**
     * Get img tag
     *
     * @return string
     */
	public function html_img($language = null) {
		if ( ! $this->image_url() ) {
			return '';
		}
		return sprintf(
			'<img src="%s" alt="%s">',
			esc_url( $this->image_url() ),
			esc_attr( $this->image_alt_text($language) )
		);
	}
}
