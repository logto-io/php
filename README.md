# Logto PHP SDK

[![Logto](https://img.shields.io/badge/for-logto-7958ff)][Website]
[![Packagist Version](https://img.shields.io/packagist/v/logto/sdk)][Packagist]
[![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/logto/sdk/php)][Packagist]
[![Packagist License](https://img.shields.io/packagist/l/logto/sdk)](https://github.com/logto-io/php)
[![Discord](https://img.shields.io/discord/965845662535147551?color=5865f2&logo=discord&label=discord)][Discord]

## Prerequisites

- PHP 8.1 or higher
- A [Logto Cloud](https://logto.io/) account or a self-hosted Logto
- A Logto traditional web application created

If you don't have the Logto application created, please follow the [⚡ Get started](https://docs.logto.io/docs/tutorials/get-started/) guide to create one.

## Installation

```bash
composer require logto/sdk
```

## Tutorial

See [tutorial](./docs/tutorial.md) for a quick start.

## API reference

See [API reference](./docs/api/index.md) for more details.

## Sample code

See [samples](./samples/) directory for example usages.

## Development scripts

### Dev

```bash
composer dev
```

This script will start a dev server at `http://localhost:5000` and use `samples/index.php` as the entry point.

### Test

```bash
composer test
```

### Update API docs

**Prerequisite**

- A `phpDocumentor.phar` in the project root (can be downloaded from [phpDocumentor](https://docs.phpdoc.org/guide/getting-started/installing.html)).
- Command `prettier` is available in the shell (an opinionated [code formatter](https://prettier.io/)), which can be installed with `npm install -g prettier`.

```bash
composer docs
```

This command will generate the API docs in `docs/api` folder and format the files.

## Resources

- [Logto website][Website]
- [Logto documentation](https://docs.logto.io/)
- [Join Discord][Discord]

[Website]: https://logto.io/
[Packagist]: https://packagist.org/packages/logto/sdk
[Discord]: https://discord.gg/vRvwuwgpVX
