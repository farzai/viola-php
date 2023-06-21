# Viola PHP (WIP)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/farzai/viola-php.svg?style=flat-square)](https://packagist.org/packages/farzai/viola-php)
[![Tests](https://img.shields.io/github/actions/workflow/status/farzai/viola-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farzai/viola-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/farzai/viola-php.svg?style=flat-square)](https://packagist.org/packages/farzai/viola-php)

Viola is a PHP package that allows you to ask questions to ChatGPT and get the answer with your own data.

## Requirements

- PHP >= 8.0
- OpenAI Key


## Installation

You can install the global package via composer:

```bash
$ composer global require farzai/viola
```

## Usage

First, you need to set your OpenAI key and Database Connection.
```bash
$ viola config

# API Key: <your-openai-key>
# Database Connection name: <your-connection-name>
# Choose database driver: <mysql|pgsql|sqlsrv>
# Enter database host, port, database name, username, password
```

Then, you can try to ask a question to ChatGPT.
```bash
$ viola ask "Show me all books by J. K. Rowling."
```

```bash
Here, I found 2 books by J. K. Rowling:

|----|------------------------------------------|---------------|------|
| id | title                                    | author        | year |
|----|------------------------------------------|---------------|------|
| 1  | Harry Potter and the Philosopher's Stone | J. K. Rowling | 1997 |
| 2  | Harry Potter and the Chamber of Secrets  | J. K. Rowling | 1998 |
|----|------------------------------------------|---------------|------|
```


## Commands

```bash
# Ask a question to ChatGPT.
$ viola ask "<your-question>"
```

```bash
# Set your OpenAI key and Database Connection.
$ viola config
```

```bash
# Show all database connections.
$ viola config:show
```

```bash
# Change current connection
$ viola use <connection-name>
```

```bash
# Clear all database connections.
$ viola config:clear all

# Or clear a specific connection.
$ viola config:clear <connection-name>
```


## Testing

```bash
$ composer test
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
