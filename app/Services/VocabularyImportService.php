<?php

namespace App\Services;

use App\Models\VocabularyWord;
use App\Models\VWordTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class VocabularyImportService
{
    public function previewFile(UploadedFile $file, array $languageCodes, int $limit = 10): array
    {
        $expectedHeaders = $this->expectedHeaders($languageCodes);
        $rows = $this->readRows($file);

        if ($rows === []) {
            return [
                'header_valid' => false,
                'expected_headers' => $expectedHeaders,
                'header_row' => [],
                'errors' => ['File is empty.'],
                'rows' => [],
                'total_rows' => 0,
            ];
        }

        $headerRow = $rows[0] ?? [];
        $normalizedHeader = $this->normalizeHeaderRow($headerRow);
        $normalizedExpected = array_map(fn ($h) => strtolower(trim($h)), $expectedHeaders);
        $headerValid = $normalizedHeader === $normalizedExpected;

        if (! $headerValid) {
            return [
                'header_valid' => false,
                'expected_headers' => $expectedHeaders,
                'header_row' => $headerRow,
                'errors' => ['Invalid header row. Expected: '.implode(', ', $expectedHeaders)],
                'rows' => [],
                'total_rows' => max(count($rows) - 1, 0),
            ];
        }

        $previewRows = [];
        $max = min(count($rows) - 1, $limit);
        for ($i = 1; $i <= $max; $i++) {
            $previewRows[] = $this->associateRow($normalizedExpected, $rows[$i] ?? []);
        }

        return [
            'header_valid' => true,
            'expected_headers' => $expectedHeaders,
            'header_row' => $headerRow,
            'errors' => [],
            'rows' => $previewRows,
            'total_rows' => max(count($rows) - 1, 0),
        ];
    }

    public function importCategoryWords(
        UploadedFile $file,
        int $categoryId,
        array $languageCodes,
    ): array {
        $expectedHeaders = $this->expectedHeaders($languageCodes);
        $rows = $this->readRows($file);
        $failed = [];

        if ($rows === []) {
            return [
                'created' => 0,
                'updated' => 0,
                'processed' => 0,
                'failed' => [
                    ['row' => 1, 'word_key' => null, 'error' => 'File is empty.'],
                ],
            ];
        }

        $headerRow = $rows[0];
        $normalizedHeader = $this->normalizeHeaderRow($headerRow);
        $normalizedExpected = array_map(fn ($h) => strtolower(trim($h)), $expectedHeaders);

        if ($normalizedHeader !== $normalizedExpected) {
            return [
                'created' => 0,
                'updated' => 0,
                'processed' => 0,
                'failed' => [
                    [
                        'row' => 1,
                        'word_key' => null,
                        'error' => 'Invalid header row. Expected: '.implode(', ', $expectedHeaders),
                    ],
                ],
            ];
        }

        $created = 0;
        $updated = 0;
        $processed = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $rowNumber = $i + 1;
            $data = $this->associateRow($normalizedExpected, $rows[$i] ?? []);
            $wordKey = trim((string) ($data['word_key'] ?? ''));
            if ($this->isEffectivelyEmptyRow($data)) {
                continue;
            }

            if ($wordKey === '') {
                $failed[] = [
                    'row' => $rowNumber,
                    'word_key' => null,
                    'error' => 'word_key is required.',
                ];

                continue;
            }

            $wordKey = strtolower($wordKey);

            try {
                $result = DB::transaction(function () use ($categoryId, $wordKey, $languageCodes, $data) {
                    $word = VocabularyWord::query()->firstOrCreate([
                        'category_id' => $categoryId,
                        'word_key' => $wordKey,
                    ]);

                    foreach ($languageCodes as $code) {
                        $w = trim((string) ($data[$code.'_word'] ?? ''));
                        $m = trim((string) ($data[$code.'_meaning'] ?? ''));
                        if ($w === '' && $m === '') {
                            continue;
                        }

                        VWordTranslation::query()->updateOrCreate(
                            [
                                'word_id' => $word->id,
                                'language_code' => $code,
                            ],
                            [
                                'word_text' => $w,
                                'meaning_text' => $m === '' ? null : $m,
                            ],
                        );
                    }

                    return [
                        'was_created' => $word->wasRecentlyCreated,
                    ];
                });

                if (($result['was_created'] ?? false) === true) {
                    $created++;
                } else {
                    $updated++;
                }

                $processed++;
            } catch (\Throwable $e) {
                $failed[] = [
                    'row' => $rowNumber,
                    'word_key' => $wordKey,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'processed' => $processed,
            'failed' => $failed,
        ];
    }

    private function expectedHeaders(array $languageCodes): array
    {
        $headers = ['word_key'];
        foreach ($languageCodes as $code) {
            $headers[] = $code.'_word';
            $headers[] = $code.'_meaning';
        }

        return $headers;
    }

    private function normalizeHeaderRow(array $headerRow): array
    {
        return array_map(fn ($h) => strtolower(trim((string) $h)), $headerRow);
    }

    private function associateRow(array $normalizedHeaders, array $row): array
    {
        $out = [];
        foreach ($normalizedHeaders as $index => $key) {
            $out[$key] = array_key_exists($index, $row) ? $this->normalizeCell($row[$index]) : '';
        }

        return $out;
    }

    private function normalizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_string($value)) {
            return trim($value);
        }

        if (is_numeric($value)) {
            return trim((string) $value);
        }

        return trim((string) $value);
    }

    private function isEffectivelyEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function readRows(UploadedFile $file): array
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $path = $file->getRealPath();
        if (! is_string($path) || $path === '') {
            return [];
        }

        if ($ext === 'xlsx') {
            $xlsx = SimpleXLSX::parse($path);
            if (! $xlsx) {
                return [];
            }

            return $xlsx->rows();
        }

        $handle = fopen($path, 'r');
        if (! is_resource($handle)) {
            return [];
        }

        $rows = [];
        $delimiter = $this->detectCsvDelimiter($handle);
        rewind($handle);

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($row === [null] || $row === false) {
                continue;
            }
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function detectCsvDelimiter($handle): string
    {
        $line = fgets($handle);
        if (! is_string($line) || $line === '') {
            return ',';
        }

        $delimiters = [',', ';', "\t", '|'];
        $best = ',';
        $bestCount = -1;
        foreach ($delimiters as $d) {
            $count = substr_count($line, $d);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $d;
            }
        }

        return $best;
    }
}
