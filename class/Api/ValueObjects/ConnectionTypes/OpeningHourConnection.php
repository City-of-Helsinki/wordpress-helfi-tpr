<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpeningHourConnection extends AbstractConnectionType
{
	public function name(): string
	{
		return 'OPENING_HOURS';
	}

	public function is_opening_hour(): bool
	{
		return true;
	}
}
