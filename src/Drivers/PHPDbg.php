<?php

declare(strict_types=1);

class PHPDbg implements TestCoverageDriverInterface
{
    private array $data = [];

    public function start(): void
    {
        \phpdbg_start_oplog();
    }

    public function end(): void
    {
        $this->data = \phpdbg_end_oplog();
    }

    public function process(): array
    {
        $result = [];
        $output = [];

        foreach ($this->data as $file => $coverage) {
            if (!str_starts_with($file, __DIR__)) {
                continue;
            }

            foreach ($coverage as $line => $value) {
                $result[$file][$line] = $value <= 0 ? 0 : 1;
            }
        }

        $lines = phpdbg_get_executable();

        foreach ($lines as $file => $coverage) {
            if (!str_starts_with($file, __DIR__)) {
                continue;
            }

            foreach ($coverage as $line => $value) {
                if (isset($result[$file][$line])) {
                    $output[$file][$line] = $result[$file][$line];
                } else {
                    $output[$file][$line] = $value;
                }
            }
        }

        return $output;
    }
}
