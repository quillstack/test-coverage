<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Mocks;

use Quillstack\TestCoverage\TestCoverageDriverInterface;

/**
 * A driver which answers with whatever it was told to, and remembers what was asked of it.
 * Real coverage needs phpdbg; what the library does with coverage does not.
 */
class FakeDriver implements TestCoverageDriverInterface
{
    public int $started = 0;

    public int $ended = 0;

    public string $processedDir = '';

    /**
     * @param array<string, array<int, int>> $result
     */
    public function __construct(private readonly array $result = [])
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public static function isAvailable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        ++$this->started;
    }

    /**
     * {@inheritDoc}
     */
    public function end(): void
    {
        ++$this->ended;
    }

    /**
     * {@inheritDoc}
     */
    public function process(string $dir = __DIR__): array
    {
        $this->processedDir = $dir;

        return $this->result;
    }
}
