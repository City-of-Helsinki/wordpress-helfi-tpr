<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames\LanguageNameInterface;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames\LanguageNameEnglish;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames\LanguageNameFinnish;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames\LanguageNameSwedish;
use CityOfHelsinki\WordPress\TPR\Api\ValueObjects\LanguageNames\LanguageNameUnknown;

class Language
{
	private LanguageNameInterface $name;

	public function __construct( LanguageNameInterface $name )
	{
		$this->name = $name;
	}

	public static function fromCode( string $code ): self
	{
		$name = match ( $code ) {
			'en' => new LanguageNameEnglish(),
			'fi' => new LanguageNameFinnish(),
			'sv' => new LanguageNameSwedish(),
			default => new LanguageNameUnknown( $code ),
		};

		return new self( $name );
	}

	public function code(): string
	{
		return $this->name->code();
	}

	public function label(): string
	{
		return $this->name->label();
	}
}
