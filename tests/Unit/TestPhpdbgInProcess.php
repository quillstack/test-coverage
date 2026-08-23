<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Unit;

use Quillstack\TestCoverage\Drivers\PHPDbg;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;

/**
 * The parts of the driver which can be measured here, because they read what is executable
 * without collecting anything — so the suite's own log is left alone.
 *
 * These only run when this process is phpdbg. Having the binary on the path is not the same
 * thing: `phpdbg_get_executable()` exists only inside it.
 */
class TestPhpdbgInProcess
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean
    ) {
        //
    }

    /**
     * Reading what is executable does not touch the log, so this part can be measured here
     * rather than out there — and it is the part that decides what counts as uncovered.
     */
    public function whatIsExecutableIsReadWithoutCollectingAnything()
    {
        $fixtures = (string) realpath(__DIR__ . '/../Fixtures');
        $output = (new PHPDbg())->process($fixtures);

        $names = array_map(
            static fn (string $path): string => basename($path),
            array_keys($output)
        );
        sort($names);

        $this->assertEqual->equal(['Covered.php', 'NeverLoaded.php'], $names);

        // Nothing was collected, so nothing is covered — every executable line is there and
        // every one of them is zero.
        foreach ($output as $lines) {
            $this->assertBoolean->isTrue($lines !== []);
            $this->assertEqual->equal(0, array_sum($lines));
        }
    }

    /**
     * A directory which is not there is not a failure; there is simply nothing in it.
     */
    public function aDirectoryWhichIsNotThereMeasuresNothing()
    {
        $this->assertEqual->equal([], (new PHPDbg())->process('/nowhere/at/all'));
    }
}
