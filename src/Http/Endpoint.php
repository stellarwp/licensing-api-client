<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http;

/**
 * Describes a versioned API endpoint path.
 */
final class Endpoint
{
	private string $path;

	private ApiVersion $version;

	public function __construct(string $path, ?ApiVersion $version = null)
	{
		$this->path    = ltrim($path, '/');
		$this->version = $version ?: ApiVersion::default();
	}

	public function path(): string
	{
		return $this->path;
	}

	public function version(): ApiVersion
	{
		return $this->version;
	}
}
