# Logto PHP SDK

Still work in progress.

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

See [API reference](./docs/api.md) for more details.

## Sample code

See [samples](./samples/) directory for example usages.

## Resources

- [Logto website](https://logto.io/)
- [Logto documentation](https://docs.logto.io/)
- [Join Discord](https://discord.gg/vRvwuwgpVX)

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
