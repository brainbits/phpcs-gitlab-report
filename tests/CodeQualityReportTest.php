<?php

declare(strict_types=1);

namespace Brainbits\Phpcs\Tests;

use Brainbits\Phpcs\CodeQualityReport;
use PHP_CodeSniffer\Files\File;
use PHPUnit\Framework\TestCase;

use function rtrim;

final class CodeQualityReportTest extends TestCase
{
    public function testGenerateFileReport(): void
    {
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $reportJson = '{"description":"This is an error","fingerprint":"3719b59c961e426a579b2ff715c24b04","severity":"major","location":{"path":"files\/TestClass.php","lines":{"begin":3}}},{"description":"This is a warning","fingerprint":"5e160d375a37b0773374bf78deb73c21","severity":"minor","location":{"path":"files\/TestClass.php","lines":{"begin":10}}},';

        $this->expectOutputString($reportJson);

        $report = new CodeQualityReport();

        $returnValue = $report->generateFileReport(
            $this->getPhpcsReport(),
            $this->createMock(File::class),
        );

        static::assertTrue($returnValue);
    }

    public function testGenerate(): void
    {
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $reportJson = '{"description":"PHP files must only contain PHP code","fingerprint":"3719b59c961e426a579b2ff715c24b04","severity":"major","location":{"path":"files\/TestClass.php","lines":{"begin":3}}}';

        $this->expectOutputString('[' . rtrim($reportJson, ',') . ']');

        $report = new CodeQualityReport();

        $report->generate($reportJson, 1, 1, 1, 1);
    }

    /** @return array{
     *     filename: string,
     *     errors: int,
     *     warnings: int,
     *     fixable: int,
     *     messages: array<int, array<int, list<array{
     *         message: string,
     *         source: string,
     *         severity: int,
     *         fixable: bool,
     *         type: string,
     *     }>>>
     * } */
    private function getPhpcsReport(): array
    {
        return [
            'filename' => 'files/TestClass.php',
            'errors' => 1,
            'warnings' => 0,
            'fixable' => 0,
            'messages' => [
                3 => [
                    1 => [
                        [
                            'message' => 'This is an error.',
                            'source' => 'Generic.Files.InlineHTML.Found',
                            'severity' => 5,
                            'fixable' => false,
                            'type' => 'ERROR',
                        ],
                    ],
                ],
                10 => [
                    1 => [
                        [
                            'message' => 'This is a warning.',
                            'source' => 'Generic.Files.InlineHTML.Found',
                            'severity' => 3,
                            'fixable' => true,
                            'type' => 'WARNING',
                        ],
                    ],
                ],
            ],
        ];
    }
}
