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

## Why this exists

Coverage in PHP usually means Xdebug or PCOV — an extension to install, in development and in
CI, before a number appears. This uses `phpdbg`, which ships with PHP itself, so measuring what
the tests reached needs nothing that is not already there.

It also counts **a file no test loaded at all**, which is the number that matters most and the
one an incomplete report quietly leaves out. A class nobody instantiated is not absent from the
report; it is zero per cent of it.

## Requirements

- PHP 8.1 or newer
- phpdbg, to measure anything — without it the tests still run, just without a number

## Installation

```shell
composer require --dev quillstack/test-coverage
```

## Usage

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

## Technical documentation

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

## Benchmark

There is no timing table here, and the reason is worth a sentence rather than a number.

Coverage is collected by the runtime — `phpdbg` here, Xdebug or PCOV for
[phpunit/php-code-coverage](https://github.com/sebastianbergmann/php-code-coverage) — and what
that costs is a property of the extension, not of the library reading its output. Timing the two
libraries would be timing three different collectors and calling the difference ours.

What can be compared honestly is what each brings with it:

| | Version | Installed | Packages |
| --- | --- | --- | --- |
| **quillstack/test-coverage** | v0.6.3 | **120 kB** | 1 |
| phpunit/php-code-coverage | 11.0.12 | 1.4 MB | 11 |

**And what those eleven packages buy is real**: branch and path coverage, not just lines; HTML,
Clover, Cobertura, Crap4J and PHP report formats; a static analysis pass that knows which lines
are executable before anything runs; and support for Xdebug and PCOV as well as phpdbg. This
produces line coverage and a Clover file.

If you already have Xdebug and want branch coverage or an HTML report, that is the one to use.

## Tests

```shell
composer test
composer test:coverage
composer stan
```

The driver is exercised in a process of its own, because phpdbg collects into one log at a
time and starting a second inside this suite would take its own measurement away from it.
That work therefore does not show up in this package's own percentage: the seven lines it
reports as uncovered are the ones only a live log reaches, and they are tested out there.

## The rest of Quillstack

This is one component of [Quillstack](https://github.com/quillstack), a PHP framework which is
as simple to use as it is strict about what it does.

- [quillstack/unit-tests](https://github.com/quillstack/unit-tests) — which runs the tests being measured
- [quillstack/benchmark](https://github.com/quillstack/benchmark) — how long things take, rather than what ran
- [quillstack/standards](https://github.com/quillstack/standards) — which checks a package is as it should be

## License

MIT. See [LICENSE](LICENSE).
