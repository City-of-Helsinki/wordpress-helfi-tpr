<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LanguageNameSwedish implements LanguageNameInterface
{
	public function code(): string
	{
		return 'sv';
	}

	public function label(): string
	{
		return __( 'Swedish', 'helsinki-tpr' );
	}
}
