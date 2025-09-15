<?php

namespace CityOfHelsinki\WordPress\TPR\Api\ValueObjects\ConnectionTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SocialMediaLinkConnection extends AbstractConnectionType
{
	public function name(): string
	{
		return 'SOCIAL_MEDIA_LINK';
	}

	public function is_link(): bool
	{
		return true;
	}
}
