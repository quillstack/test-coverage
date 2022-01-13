<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\CoverageOutput;

use Quillstack\TestCoverage\TestCoverageOutputInterface;

class CoverageXml implements TestCoverageOutputInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(array $input, string $rootDir = ''): string
    {
        $xml = new \SimpleXMLElement('<coverage/>');
        $project = $xml->addChild('project');

        foreach ($input as $file => $lines) {
            $class = $project->addChild('file');

            if ($rootDir) {
                $file = str_replace($rootDir, '', $file);
            }

            $class->addAttribute('name', $file);

            foreach ($lines as $line => $value) {
                $row = $class->addChild('line');
                $row->addAttribute('num', (string) $line);
                $row->addAttribute('count', (string) $value);
            }
        }

        return $xml->asXML();
    }
}
