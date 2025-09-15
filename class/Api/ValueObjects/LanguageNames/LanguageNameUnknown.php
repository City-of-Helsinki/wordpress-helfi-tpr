<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LanguageNameUnknown implements LanguageNameInterface
{
	private string $code;

	public function __construct( string $code )
	{
		$this->code = $code;
	}

	public function code(): string
	{
		return $this->code;
	}

	public function label(): string
	{
		return $this->code;
	}
}
