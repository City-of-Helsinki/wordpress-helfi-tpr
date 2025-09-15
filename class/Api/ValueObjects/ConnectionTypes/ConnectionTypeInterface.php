<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\Connection;

interface ConnectionTypeInterface
{
	public function name(): string;

	public function is_opening_hour(): bool;
	public function is_link(): bool;
	public function is_eservice_link(): bool;

	public function to_html( Connection $connection, string $lang ): string;
}
