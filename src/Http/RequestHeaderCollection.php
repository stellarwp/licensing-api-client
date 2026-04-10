<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Http;

/**
 * Immutable request-header state shared across resource views.
 *
 * @phpstan-import-type HeaderValue from RequestBuilder
 */
final class RequestHeaderCollection
{
	/**
	 * @var array<string, array{name: string, value: HeaderValue}>
	 */
	private array $headers;

	/**
	 * @param array<string, HeaderValue> $headers
	 */
	public function __construct(array $headers = []) {
		$this->headers = $this->normalizeHeaders($headers);
	}

	/**
	 * @return array<string, HeaderValue>
	 */
	public function all(): array {
		$headers = [];

		foreach ($this->headers as $entry) {
			$headers[$entry['name']] = $entry['value'];
		}

		return $headers;
	}

	public function withoutHeaders(): self {
		if ($this->headers === []) {
			return $this;
		}

		return new self();
	}

	/**
	 * @param array<string, HeaderValue> $headers
	 */
	public function withHeaders(array $headers): self {
		if ($headers === []) {
			return $this;
		}

		$merged = array_replace($this->headers, $this->normalizeHeaders($headers));

		if ($merged === $this->headers) {
			return $this;
		}

		return self::fromNormalized($merged);
	}

	public function withTraceId(string $traceId): self {
		return $this->withHeaders([
			'X-Trace-Id' => $traceId,
		]);
	}

	/**
	 * @param array<string, HeaderValue> $headers
	 *
	 * @return array<string, HeaderValue>
	 */
	public function merge(array $headers): array {
		return $this->withHeaders($headers)->all();
	}

	/**
	 * @param array<string, array{name: string, value: HeaderValue}> $headers
	 */
	private static function fromNormalized(array $headers): self {
		$self          = new self();
		$self->headers = $headers;

		return $self;
	}

	/**
	 * @param array<string, HeaderValue> $headers
	 *
	 * @return array<string, array{name: string, value: HeaderValue}>
	 */
	private function normalizeHeaders(array $headers): array {
		$normalized = [];

		foreach ($headers as $name => $value) {
			$name = trim((string) $name);

			if ($name === '') {
				continue;
			}

			$normalized[strtolower($name)] = [
				'name'  => $name,
				'value' => $value,
			];
		}

		return $normalized;
	}
}
