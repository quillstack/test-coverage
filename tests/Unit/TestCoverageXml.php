<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Unit;

use Quillstack\TestCoverage\CoverageOutput\CoverageXml;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;
use SimpleXMLElement;

/**
 * The file SonarCloud reads. If this is wrong, every coverage number in the organisation is
 * wrong with it, and nothing in between would notice.
 */
class TestCoverageXml
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertBoolean $assertBoolean
    ) {
        //
    }

    public function everyFileAndEveryLineIsInIt()
    {
        $xml = new SimpleXMLElement((new CoverageXml())->generate([
            '/app/src/A.php' => [3 => 1, 4 => 0],
            '/app/src/B.php' => [7 => 1],
        ]));

        $files = $xml->project->file;

        $this->assertEqual->equal(2, count($files));
        $this->assertEqual->equal('/app/src/A.php', (string) $files[0]['name']);
        $this->assertEqual->equal(2, count($files[0]->line));
        $this->assertEqual->equal('3', (string) $files[0]->line[0]['num']);
        $this->assertEqual->equal('1', (string) $files[0]->line[0]['count']);
        $this->assertEqual->equal('4', (string) $files[0]->line[1]['num']);
        $this->assertEqual->equal('0', (string) $files[0]->line[1]['count']);
        $this->assertEqual->equal('/app/src/B.php', (string) $files[1]['name']);
    }

    /**
     * The paths are relative to the project, because the report is read somewhere other than
     * the machine which wrote it — an absolute path from a CI runner matches nothing.
     */
    public function theRootIsTakenOffThePaths()
    {
        $xml = new SimpleXMLElement((new CoverageXml())->generate(
            ['/home/runner/work/orm/orm/src/Orm.php' => [10 => 1]],
            '/home/runner/work/orm/orm'
        ));

        $this->assertEqual->equal('/src/Orm.php', (string) $xml->project->file[0]['name']);
    }

    public function withoutARootThePathsAreLeftAlone()
    {
        $xml = new SimpleXMLElement((new CoverageXml())->generate(['/src/A.php' => [1 => 1]]));

        $this->assertEqual->equal('/src/A.php', (string) $xml->project->file[0]['name']);
    }

    /**
     * Nothing measured is still a report, and still one a reader can parse.
     */
    public function nothingMeasuredIsStillValidXml()
    {
        $output = (new CoverageXml())->generate([]);
        $xml = new SimpleXMLElement($output);

        $this->assertBoolean->isTrue(str_contains($output, '<coverage>'));
        $this->assertEqual->equal(0, count($xml->project->file));
    }

    /**
     * A path holding a character XML cares about must not close the attribute it sits in.
     */
    public function aPathIsEscaped()
    {
        $output = (new CoverageXml())->generate(['/src/A&B<C>.php' => [1 => 1]]);
        $xml = new SimpleXMLElement($output);

        $this->assertBoolean->isFalse(str_contains($output, '<C>.php'));
        $this->assertEqual->equal('/src/A&B<C>.php', (string) $xml->project->file[0]['name']);
    }

    /**
     * A file with no executable lines at all is still named, so it counts as uncovered
     * rather than disappearing.
     */
    public function aFileWithNoLinesIsStillNamed()
    {
        $xml = new SimpleXMLElement((new CoverageXml())->generate(['/src/Empty.php' => []]));

        $this->assertEqual->equal(1, count($xml->project->file));
        $this->assertEqual->equal(0, count($xml->project->file[0]->line));
    }
}
