<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LanguageNameEnglish implements LanguageNameInterface
{
	public function code(): string
	{
		return 'en';
	}

	public function label(): string
	{
		return __( 'English', 'helsinki-tpr' );
	}
}
