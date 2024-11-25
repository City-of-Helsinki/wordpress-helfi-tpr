<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UnknownConnection extends AbstractConnectionType
{
	public function name(): string
	{
		return '';
	}
}
