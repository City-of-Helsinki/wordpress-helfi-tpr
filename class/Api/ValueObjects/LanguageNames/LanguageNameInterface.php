<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface LanguageNameInterface
{
	public function code(): string;
	public function label(): string;
}
