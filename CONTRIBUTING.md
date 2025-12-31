# Contributing

Thank you for considering contributing to PDFLib! We welcome contributions from the community to help make this the number one PDF manipulation library for PHP.

## Development Setup

1.  **Requirements**: Ensure you have PHP 8.1+ and Ghostscript installed.
2.  **Install Dependencies**:
    ```bash
    composer install
    ```

## Architecture Overview

PDFLib v3.0+ uses a **Driver Pattern**.
- **`src/Contracts/DriverInterface.php`**: Defines the contract for all drivers.
- **`src/Drivers/GhostscriptDriver.php`**: The default implementation using `symfony/process`.
- **`src/PDF.php`**: The modern fluent Facade.

When contributing features, please ensure they are implemented in the `DriverInterface` and supported by the `GhostscriptDriver`.

## Code Quality Standards

We maintain high code quality using automated tools. Please run these before submitting a PR.

### Formatting (Pint)
We follow PSR-12 style via Laravel Pint.
```bash
composer format
# or
vendor/bin/pint
```

### Static Analysis (PHPStan)
Ensure your code passes static analysis level 5.
```bash
composer analyze
# or
vendor/bin/phpstan analyse
```

### Testing (PHPUnit 10)
All new features must include tests. We use PHPUnit 10.
```bash
composer test
# or
vendor/bin/phpunit
```

## Pull Request Guidelines

1.  **Feature Branches**: Create a branch for your feature (e.g., `feature/html-to-pdf`).
2.  **One Feature per PR**: Keep PRs focused on a single change.
3.  **Tests**: Include tests that cover your changes.
4.  **Documentation**: Update the README if you change public API behavior.
5.  **Commit History**: Keep commits meaningful and atomic. Squash if necessary.

## Release Cycle
We follow [SemVer v2.0.0](http://semver.org/). Breaking changes should be discussed in an issue first.