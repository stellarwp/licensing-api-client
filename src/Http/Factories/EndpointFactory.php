<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http\Factories;

use StellarWP\LicensingApiClient\Http\ApiVersion;
use StellarWP\LicensingApiClient\Http\Endpoint;

/**
 * Builds versioned endpoint objects with a configurable default API version.
 */
final class EndpointFactory
{
	private ApiVersion $defaultVersion;

	public function __construct(ApiVersion $defaultVersion)
	{
		$this->defaultVersion = $defaultVersion;
	}

	public function make(string $path): Endpoint
	{
		return new Endpoint($path, $this->defaultVersion);
	}

	public function makeWithVersion(string $path, ApiVersion $version): Endpoint
	{
		return new Endpoint($path, $version);
	}
}
