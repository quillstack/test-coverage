<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Unit;

use Quillstack\TestCoverage\Drivers\NoCoverage;
use Quillstack\TestCoverage\Tests\Mocks\FakeDriver;
use Quillstack\TestCoverage\Tests\Mocks\FakeOutput;
use Quillstack\TestCoverage\TestCoverage;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;

/**
 * The number every one of these packages puts in its README comes from here, so it is worth
 * knowing it adds up.
 */
class TestSummary
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean
    ) {
        //
    }

    /**
     * @param array<string, array<int, int>> $coverage
     */
    private function summaryOf(array $coverage): array
    {
        $coverageTool = new TestCoverage(new FakeDriver($coverage), new FakeOutput());
        $coverageTool->process('/src');

        return $coverageTool->getSummary();
    }

    public function everyExecutableLineIsCountedAndEveryCoveredOneToo()
    {
        $summary = $this->summaryOf([
            '/src/A.php' => [3 => 1, 4 => 1, 5 => 0],
            '/src/B.php' => [7 => 1],
        ]);

        $this->assertEqual->equal(3, $summary['covered']);
        $this->assertEqual->equal(4, $summary['total']);
        $this->assertEqual->equal(75.0, $summary['percent']);
        $this->assertEqual->equal(2, $summary['files']);
    }

    /**
     * A file no test ever loaded still counts, uncovered. Leaving it out is what made the
     * framework read 94.6% when it was 80.3%.
     */
    public function aFileWithNothingCoveredStillCounts()
    {
        $summary = $this->summaryOf([
            '/src/A.php' => [3 => 1, 4 => 1],
            '/src/NeverLoaded.php' => [3 => 0, 4 => 0, 5 => 0, 6 => 0],
        ]);

        $this->assertEqual->equal(2, $summary['covered']);
        $this->assertEqual->equal(6, $summary['total']);
        $this->assertEqual->equal(33.3, $summary['percent']);
        $this->assertEqual->equal(2, $summary['files']);
    }

    /**
     * Nothing to measure is nothing measured, rather than a division by zero.
     */
    public function nothingAtAllIsNotAFailure()
    {
        $summary = $this->summaryOf([]);

        $this->assertEqual->equal(0, $summary['covered']);
        $this->assertEqual->equal(0, $summary['total']);
        $this->assertEqual->equal(0.0, $summary['percent']);
        $this->assertEqual->equal(0, $summary['files']);
    }

    public function everythingCoveredIsAHundred()
    {
        $summary = $this->summaryOf(['/src/A.php' => [1 => 1, 2 => 1, 3 => 1]]);

        $this->assertEqual->equal(100.0, $summary['percent']);
    }

    public function nothingCoveredIsZero()
    {
        $summary = $this->summaryOf(['/src/A.php' => [1 => 0, 2 => 0]]);

        $this->assertEqual->equal(0.0, $summary['percent']);
    }

    /**
     * One decimal place, so 2 of 3 reads 66.7 rather than 66.66666666666667.
     */
    public function thePercentIsRoundedToOneDecimal()
    {
        $this->assertEqual->equal(66.7, $this->summaryOf(['/a' => [1 => 1, 2 => 1, 3 => 0]])['percent']);
        $this->assertEqual->equal(99.9, $this->summaryOf(['/a' => array_merge(
            array_fill(1, 999, 1),
            [1000 => 0]
        )])['percent']);
    }

    /**
     * Asking before anything has been processed answers nothing, not the run before.
     */
    public function nothingProcessedIsNothingToSummarise()
    {
        $summary = (new TestCoverage(new FakeDriver(), new FakeOutput()))->getSummary();

        $this->assertEqual->equal(0, $summary['total']);
    }

    /**
     * Whether coverage can be measured at all is what decides between a number and the note
     * saying there is none.
     */
    public function itSaysWhetherItCanMeasureAnything()
    {
        $this->assertBoolean->isTrue(
            (new TestCoverage(new FakeDriver(), new FakeOutput()))->isAvailable()
        );
        $this->assertBoolean->isFalse(
            (new TestCoverage(new NoCoverage(), new FakeOutput()))->isAvailable()
        );
    }
}
