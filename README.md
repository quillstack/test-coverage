# Quillstack Test Coverage

[![Tests](https://github.com/quillstack/test-coverage/actions/workflows/tests.yml/badge.svg)](https://github.com/quillstack/test-coverage/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/quillstack/test-coverage.svg)](https://packagist.org/packages/quillstack/test-coverage)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/test-coverage.svg)](https://packagist.org/packages/quillstack/test-coverage)
[![PHP Version](https://img.shields.io/packagist/php-v/quillstack/test-coverage)](https://packagist.org/packages/quillstack/test-coverage)
[![StyleCI](https://github.styleci.io/repos/415300480/shield?branch=main)](https://github.styleci.io/repos/415300480?branch=main)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/test-coverage/badge)](https://www.codefactor.io/repository/github/quillstack/test-coverage)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=quillstack_test-coverage&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=quillstack_test-coverage)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_test-coverage&metric=coverage)](https://sonarcloud.io/summary/new_code?id=quillstack_test-coverage)
[![Maintainability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_test-coverage&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_test-coverage)
[![Reliability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_test-coverage&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_test-coverage)
[![Security](https://sonarcloud.io/api/project_badges/measure?project=quillstack_test-coverage&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_test-coverage)
[![License](https://img.shields.io/packagist/l/quillstack/test-coverage)](https://github.com/quillstack/test-coverage/blob/main/LICENSE)

A library to create test coverage reports in PHP. Full documentation:
https://quillstack.org/test-coverage

What [quillstack/unit-tests](https://github.com/quillstack/unit-tests) uses to say how much of
a package its tests actually run, and to write the report SonarCloud reads. It needs no
extension: coverage comes from phpdbg, which ships with PHP.

### Requirements

- PHP 8.1 or newer
- phpdbg, to measure anything — without it the tests still run, just without a number

### Installation

```shell
composer require --dev quillstack/test-coverage
```

### Usage

```php
use Quillstack\TestCoverage\CoverageOutput\CoverageXml;
use Quillstack\TestCoverage\Drivers\PHPDbg;
use Quillstack\TestCoverage\Drivers\NoCoverage;
use Quillstack\TestCoverage\TestCoverage;

$coverage = new TestCoverage(
    PHPDbg::isAvailable() ? new PHPDbg() : new NoCoverage(),
    new CoverageXml()
);

$coverage->start();
// … run the tests …
$coverage->end();

$xml = $coverage->process(__DIR__ . '/src', __DIR__);
$summary = $coverage->getSummary();
// ['covered' => 54, 'total' => 61, 'percent' => 88.5, 'files' => 4]
```

Run it under phpdbg, which is a separate binary shipped with PHP:

```shell
phpdbg -qrr vendor/bin/unit-tests
```

### A file nothing loaded still counts

phpdbg knows nothing about a file which was never compiled, so a class no test ever touched
would drop out of the report altogether — and the percentage would come out better than the
truth. Every file under the directory is compiled first, which puts its lines back into the
total as uncovered.

It is not a small difference: it took one package in this stack from a reported 94.6% to an
honest 80.3%.

### Technical documentation

| Class | What it is |
| --- | --- |
| `TestCoverage` | the way in: starts, stops, processes, and summarises |
| `Drivers\PHPDbg` | measures with phpdbg |
| `Drivers\NoCoverage` | measures nothing, so tests still run where phpdbg is not there |
| `CoverageOutput\CoverageXml` | writes the report Sonar and friends read |

`TestCoverage` implements `TestCoverageInterface`:

| Method | Does |
| --- | --- |
| `isAvailable(): bool` | whether anything can be measured at all |
| `start(): void` / `end(): void` | begin and end collecting |
| `process(string $dir, string $rootDir = ''): string` | the report, as a string |
| `getSummary(): array` | `covered`, `total`, `percent`, `files` of the last run |

`$rootDir` is taken off the front of every path, because the report is read somewhere other
than the machine which wrote it and an absolute path from a CI runner matches nothing.

A driver implements `TestCoverageDriverInterface` (`isAvailable()`, `start()`, `end()`,
`process()`), and an output implements `TestCoverageOutputInterface` (`generate()`) — so
another way of measuring, or another format, is one class either way.

### Unit tests

```shell
composer test
composer test:coverage
composer stan
```

The driver is exercised in a process of its own, because phpdbg collects into one log at a
time and starting a second inside this suite would take its own measurement away from it.
That work therefore does not show up in this package's own percentage: the seven lines it
reports as uncovered are the ones only a live log reaches, and they are tested out there.

### License

MIT. See [LICENSE](LICENSE).
