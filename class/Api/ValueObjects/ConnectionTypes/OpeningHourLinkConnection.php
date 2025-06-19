<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpeningHourLinkConnection extends AbstractConnectionType
{
	public function name(): string
	{
		return 'OPENING_HOURS';
	}

	public function is_link(): bool
	{
		return true;
	}

	public function is_opening_hour(): bool
	{
		return true;
	}
}
