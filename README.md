# Licensing API Client

> ⚠️ **This is a read-only repository!**
> For pull requests or issues, see [stellarwp/licensing-api-client-monorepo](https://github.com/stellarwp/licensing-api-client-monorepo).

A PHP client for the v4 StellarWP Licensing API.

💡 In most cases you should use one of the transport-specific client packages instead of installing this package directly unless you plan to provide your own HTTP client:

- [stellarwp/licensing-api-client-wordpress](https://github.com/stellarwp/licensing-api-client-wordpress)
- [stellarwp/licensing-api-client-guzzle](https://github.com/stellarwp/licensing-api-client-guzzle)

This package is the core API layer they build on top of.

## Installation

Update your composer.json and add the following to your `repositories` object:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:stellarwp/licensing-api-client.git"
        }
    ]
}
```

Then, install:

```shell
composer require stellarwp/licensing-api-client
```

## Examples

For end-to-end API cookbook examples, see:

- [API Examples](https://github.com/stellarwp/licensing-api-client-monorepo/blob/main/docs/examples/index.md)

## Status

This package is being developed in the monorepo and published as a read-only split repository.
