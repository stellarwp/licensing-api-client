# Licensing API Client

> ⚠️ **This is a read-only repository!**
> For pull requests or issues, see [stellarwp/licensing-api-client-monorepo](https://github.com/stellarwp/licensing-api-client-monorepo).

A PHP client for the Liquid Web Licensing API.

💡 In most cases you should use one of the transport-specific client packages instead of installing this package directly unless you plan to provide your own HTTP client:

- [stellarwp/licensing-api-client-wordpress](https://github.com/stellarwp/licensing-api-client-wordpress)
- [stellarwp/licensing-api-client-guzzle](https://github.com/stellarwp/licensing-api-client-guzzle)

This package is the core API layer they build on top of.

## Installation

Install with composer:

```shell
composer require stellarwp/licensing-api-client
```

## Examples

For end-to-end API cookbook examples, see:

- [API Examples](https://github.com/stellarwp/licensing-api-client-monorepo/blob/main/docs/examples/index.md)

Short example:

```php
<?php declare(strict_types=1);

$traceId = bin2hex(random_bytes(16));

$catalog = $api
	->withTraceId($traceId)
	->products()
	->catalog('LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N', 'example.com');
```

Use `withTraceId()` when you want your own request or workflow identifier to be forwarded as `X-Trace-Id`. The licensing service uses that header as its request `trace_id` and includes it in the Axiom trace/log pipeline. Use `withHeaders()` for other custom headers.

## Status

This package is being developed in the monorepo and published as a read-only split repository.
