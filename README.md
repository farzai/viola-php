# Viola PHP (WIP)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/farzai/viola-php.svg?style=flat-square)](https://packagist.org/packages/farzai/viola-php)
[![Tests](https://img.shields.io/github/actions/workflow/status/farzai/viola-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farzai/viola-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/farzai/viola-php.svg?style=flat-square)](https://packagist.org/packages/farzai/viola-php)

Viola is a PHP package that allows you to ask questions to ChatGPT API and get the answer with your own data.

## Requirements
```bash
# Your PHP In local machine
- PHP >= 8.0 (with ext-json)

# ChatGPT API
- OpenAI API key
```

## Installation

You can install the global package via composer:

```bash
composer global require farzai/viola:dev-main
```

## Usage

```bash
viola "Show me total sales for the last 3 days"
```

Response:
```bash
For the last 3 days, total sales is 1000
```

Or, you can use the `--table` option to display the result in a table format:

```bash
viola "Show me total sales for the last 3 days" --table
```

Response:
```bash
+---------------------+---------------------+
| For the last 3 days | total               |
+---------------------+---------------------+
| 3 days              | 1000                |
+---------------------+---------------------+
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [parsilver](https://github.com/parsilver)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
