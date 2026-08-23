<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Unit;

use Quillstack\TestCoverage\Drivers\PHPDbg;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;

/**
 * The driver, really measuring something.
 *
 * It runs in a process of its own: phpdbg collects into one log at a time, so starting a
 * second one inside this suite would take the suite's own measurement away from it.
 */
class TestPhpdbgDriver
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean
    ) {
        //
    }

    /**
     * @return array{available: bool, summary: array<string, mixed>, xml: string}
     */
    private function run(): array
    {
        $script = escapeshellarg(__DIR__ . '/../Support/phpdbg-run.php');
        $output = (string) shell_exec("phpdbg -qrr {$script} 2>/dev/null");

        /** @var array{available: bool, summary: array<string, mixed>, xml: string} $decoded */
        $decoded = json_decode($output, true);

        return $decoded;
    }

    /**
     * Two files, eight executable lines, and only the ones that ran counted as run.
     */
    public function itMeasuresWhatActuallyRan()
    {
        $result = $this->run();

        $this->assertBoolean->isTrue($result['available']);
        $this->assertEqual->equal(2, $result['summary']['files']);
        $this->assertEqual->equal(8, $result['summary']['total']);
        $this->assertEqual->equal(4, $result['summary']['covered']);
    }

    /**
     * The one that matters: a file nothing ever loaded is in the report, uncovered.
     *
     * phpdbg knows nothing about a file which was never compiled, so without compiling those
     * first they drop out of the total altogether — and the number comes out better than the
     * truth. This is what took one package from a reported 94.6% to an honest 80.3%.
     */
    public function aFileNothingLoadedIsCountedAgainstIt()
    {
        $result = $this->run();

        $this->assertBoolean->isTrue(str_contains($result['xml'], 'NeverLoaded.php'));

        // Every line of it uncovered, and none of them missing.
        preg_match('#<file name="/NeverLoaded.php">(.*?)</file>#s', $result['xml'], $matches);
        $lines = $matches[1] ?? '';

        $this->assertBoolean->isTrue($lines !== '');
        $this->assertBoolean->isFalse(str_contains($lines, 'count="1"'));
    }

    /**
     * A method nobody called is executable and uncovered, in the same file as one that ran.
     */
    public function anUncalledMethodIsUncoveredInACoveredFile()
    {
        $result = $this->run();

        preg_match('#<file name="/Covered.php">(.*?)</file>#s', $result['xml'], $matches);
        $lines = $matches[1] ?? '';

        $this->assertBoolean->isTrue(str_contains($lines, 'count="1"'));
        $this->assertBoolean->isTrue(str_contains($lines, 'count="0"'));
    }

    /**
     * The paths are relative to the project, because the report is read somewhere else.
     */
    public function thePathsAreRelative()
    {
        $result = $this->run();

        $this->assertBoolean->isTrue(str_contains($result['xml'], 'name="/Covered.php"'));
        $this->assertBoolean->isFalse(str_contains($result['xml'], 'name="/Users'));
        $this->assertBoolean->isFalse(str_contains($result['xml'], 'name="/home'));
    }
}
