<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Connection;

abstract class AbstractConnectionType implements ConnectionTypeInterface
{
	public function is_opening_hour(): bool
	{
		return false;
	}

	public function is_link(): bool
	{
		return false;
	}

	public function is_eservice_link(): bool
	{
		return false;
	}

	public function to_html( Connection $connection, string $lang ): string
	{
		return $connection->is_link()
			? $this->link_html( $connection, $lang )
			: $this->text_html( $connection, $lang );
	}

	protected function link_html( Connection $connection, string $lang ): string
	{
		$url = $connection->url( $lang );
		$anchor = $connection->name( $lang );

		return ( $url && $anchor ) ? sprintf(
			'<p><a href="%s">%s</a></p>',
			\esc_url( $url ),
			\esc_html( $anchor )
		) : $this->text_html( $connection, $lang );
	}

	protected function text_html( Connection $connection, string $lang ): string
	{
		$content = $connection->name( $lang );

		return $content ? \wp_kses_post( \wpautop( $content ) ) : '';
	}
}
