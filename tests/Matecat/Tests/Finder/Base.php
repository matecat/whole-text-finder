<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 20/02/26
 * Time: 17:27
 *
 */
declare(strict_types=1);

namespace Matecat\Finder\Tests;

use PHPUnit\Framework\TestCase;

abstract class Base extends TestCase
{
    protected function getTestFile(string $file): string
    {
        return (string)file_get_contents($this->getTestFilePath($file));
    }

    protected function getTestFilePath(string $file): string
    {
        return __DIR__ . '/../../../files/' . $file;
    }

    protected function markTestSkippedInCoverage(): void
    {
        $isCoverage = (bool)count(
            array_filter(
                $_SERVER['argv'],
                fn($arg) => str_contains($arg, 'coverage') && !str_contains($arg, 'no-coverage')
            )
        );

        if ($isCoverage) {
            $this->markTestSkipped(
                'This test is very expensive when coverage is enabled.',
            );
        }
    }

}