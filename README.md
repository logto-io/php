# Logto PHP SDK

Still work in progress.

## Development scripts

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
