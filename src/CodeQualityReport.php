<?php

declare(strict_types=1);

namespace Brainbits\Phpcs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reports\Report;

use function json_encode;
use function md5;
use function rtrim;
use function sprintf;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
// phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName

final readonly class CodeQualityReport implements Report
{
    /**
     * @param array{
     *      filename: string,
     *      errors: int,
     *      warnings: int,
     *      fixable: int,
     *      messages: array<int, array<int, list<array{
     *          message: string,
     *          source: string,
     *          severity: int,
     *          fixable: bool,
     *          type: string,
     *      }>>>
     *  } $report
     * @param bool   $showSources
     * @param int    $width
     */
    public function generateFileReport($report, File $phpcsFile, $showSources = false, $width = 80): bool
    {
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $columnErrors) {
                foreach ($columnErrors as $error) {
                    $data = [
                        'description' => rtrim($error['message'], '.'),
                        'fingerprint' => md5(sprintf(
                            '%s-%s-%s-%s',
                            $error['source'],
                            $report['filename'],
                            $line,
                            $column,
                        )),
                        'severity' => $this->getSeverity($error['type']),
                        'location' => [
                            'path' => $report['filename'],
                            'lines' => ['begin' => $line],
                        ],
                    ];

                    echo json_encode($data) . ',';
                }
            }
        }

        return true;
    }

    /**
     * @param string $cachedData    Any partial report data that was returned from
     *                              generateFileReport during the run.
     * @param int    $totalFiles    Total number of files processed during the run.
     * @param int    $totalErrors   Total number of errors found during the run.
     * @param int    $totalWarnings Total number of warnings found during the run.
     * @param int    $totalFixable  Total number of problems that can be fixed.
     * @param bool   $showSources   Show sources?
     * @param int    $width         Maximum allowed line width.
     * @param bool   $interactive   Are we running in interactive mode?
     * @param bool   $toScreen      Is the report being printed to screen?
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources = false,
        $width = 80,
        $interactive = false,
        $toScreen = true,
    ): void {
        echo '[' . rtrim($cachedData, ',') . ']';
    }

    private function getSeverity(mixed $errorType): string
    {
        // info, minor, major, critical, or blocker

        return match ($errorType) {
            'ERROR' => 'major',
            'WARNING' => 'minor',
            default => 'info',
        };
    }
}
