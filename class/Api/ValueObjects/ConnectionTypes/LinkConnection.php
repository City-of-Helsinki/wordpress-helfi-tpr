<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LinkConnection extends AbstractConnectionType
{
	public function name(): string
	{
		return 'LINK';
	}

	public function is_link(): bool
	{
		return true;
	}
}
