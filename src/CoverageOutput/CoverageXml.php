<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\CoverageOutput;

class CoverageXml implements TestCoverageOutputInterface
{
    public function generate(array $input): string
    {
        $xml = new \SimpleXMLElement('<coverage/>');
        $project = $xml->addChild('project');

        foreach ($input as $file => $lines) {
            $class = $project->addChild('file');
            $class->addAttribute('name', $file);

            foreach ($lines as $line => $value) {
                $row = $class->addChild('line');
                $row->addAttribute('num', $line);
                $row->addAttribute('count', $value);
            }
        }

        return $xml->asXML();
    }
}
