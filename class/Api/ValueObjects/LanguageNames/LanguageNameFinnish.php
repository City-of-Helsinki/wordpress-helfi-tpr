<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LanguageNameFinnish implements LanguageNameInterface
{
	public function code(): string
	{
		return 'fi';
	}

	public function label(): string
	{
		return __( 'Finnish', 'helsinki-tpr' );
	}
}
