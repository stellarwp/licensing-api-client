<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Contracts;

use StellarWP\LicensingApiClient\Resources\Contracts\CreditsResourceInterface;
use StellarWP\LicensingApiClient\Resources\Contracts\EntitlementsResourceInterface;
use StellarWP\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use StellarWP\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;

/**
 * Defines the root entrypoint for the Licensing API client.
 */
interface LicensingClientInterface
{
	public function entitlements(): EntitlementsResourceInterface;

	public function licenses(): LicensesResourceInterface;

	public function products(): ProductsResourceInterface;

	public function credits(): CreditsResourceInterface;

	public function withoutAuth(): self;

	public function withConfiguredToken(): self;

	public function withToken(string $token): self;
}
