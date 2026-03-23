<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Contracts;

use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\EntitlementsResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;

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
