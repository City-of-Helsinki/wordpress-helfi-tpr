<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EServiceLinkConnection extends AbstractConnectionType
{
	public function name(): string
	{
		return 'ESERVICE_LINK';
	}

	public function is_link(): bool
	{
		return true;
	}

	public function is_eservice_link(): bool
	{
		return true;
	}
}
