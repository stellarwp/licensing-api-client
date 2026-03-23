<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http;

/**
 * Represents a fully resolved licensing API URI.
 */
final class ApiUri
{
	private string $uri;

	public function __construct(string $uri)
	{
		$this->uri = $uri;
	}

	public function uri(): string
	{
		return $this->uri;
	}
}
