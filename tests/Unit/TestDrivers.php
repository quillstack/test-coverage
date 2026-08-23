<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Unit;

use Quillstack\TestCoverage\Drivers\NoCoverage;
use Quillstack\TestCoverage\Drivers\PHPDbg;
use Quillstack\TestCoverage\Tests\Mocks\FakeDriver;
use Quillstack\TestCoverage\Tests\Mocks\FakeOutput;
use Quillstack\TestCoverage\TestCoverage;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;

class TestDrivers
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean
    ) {
        //
    }

    /**
     * Without a driver the tests still run; they just run without a number at the end.
     */
    public function withoutADriverThereIsNothingToMeasure()
    {
        $driver = new NoCoverage();
        $driver->start();
        $driver->end();

        $this->assertBoolean->isTrue(NoCoverage::isAvailable());
        $this->assertEqual->equal([], $driver->process('/src'));
    }

    /**
     * phpdbg is what measures it, and whether it is there is a fact about the runtime rather
     * than a setting.
     */
    public function phpdbgIsAvailableExactlyWhenItIsThere()
    {
        $this->assertEqual->equal(
            function_exists('phpdbg_start_oplog'),
            PHPDbg::isAvailable()
        );
    }

    /**
     * Everything asked of the coverage is asked of the driver, once each.
     */
    public function whatIsAskedOfItIsAskedOfTheDriver()
    {
        $driver = new FakeDriver(['/src/A.php' => [1 => 1]]);
        $output = new FakeOutput();
        $coverage = new TestCoverage($driver, $output);

        $coverage->start();
        $coverage->end();
        $result = $coverage->process('/src', '/app');

        $this->assertEqual->equal(1, $driver->started);
        $this->assertEqual->equal(1, $driver->ended);
        $this->assertEqual->equal('/src', $driver->processedDir);
        $this->assertEqual->equal('generated', $result);
    }

    /**
     * And what the driver found is what the output is given, unchanged.
     */
    public function whatTheDriverFoundIsWhatIsWrittenOut()
    {
        $found = ['/src/A.php' => [1 => 1, 2 => 0]];
        $output = new FakeOutput();

        (new TestCoverage(new FakeDriver($found), $output))->process('/src', '/app');

        $this->assertEqual->equal($found, $output->received);
        $this->assertEqual->equal('/app', $output->rootDir);
    }
}
