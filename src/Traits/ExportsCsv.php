<?php


namespace twa\smsautils\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Storage;

trait ExportsCsv
{
    protected function ensureCsvExtension(string $name): string
    {
        return preg_match('/\.csv$/i', $name) ? $name : preg_replace('/\.[^.]+$/', '', $name) . '.csv';
    }

    protected function makeCsvExportObject(array $rows, array $headers): object
    {
        return new class($rows, $headers) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithCustomCsvSettings {
            protected $data;
            protected $headings;

            public function __construct($data, $headings)
            {
                $this->data = $data;
                $this->headings = $headings;
            }

            public function array(): array
            {
                return array_map(function ($row) {
                    return array_map(function ($value) {
                        $stringValue = (string) $value;

                        // Force large numbers or numbers with leading zeros to text
                        if (is_numeric($value) && (strlen($stringValue) > 10 || str_starts_with($stringValue, '0'))) {
                            return '="' . str_replace('"', '""', $stringValue) . '"';
                        }

                        return $value;
                    }, array_values($row)); // make sure row is indexed
                }, $this->data);
            }

            public function headings(): array
            {
                return $this->headings;
            }

            public function getCsvSettings(): array
            {
                return [
                    'use_bom' => true,
                    'delimiter' => ',',
                    'enclosure' => '"',
                    'escape_character' => '"',
                ];
            }
        };
    }

    protected function downloadCsv(array $headers, array $rows, string $filename, ?array $textColumns = null)
    {
        return response()->streamDownload(function () use ($headers, $rows, $textColumns) {

            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'w');

            // Write headers with proper encoding
            $encodedHeaders = array_map(function ($header) {
                return mb_convert_encoding($header, 'UTF-8', 'UTF-8');
            }, $headers);
            fputcsv($output, $encodedHeaders, ',', '"');


            foreach ($rows as $row) {
                $encodedRow = [];
                foreach ($row as $index => $value) {

                    $stringValue = (string) $value;
                    $stringValue = mb_convert_encoding($stringValue, 'UTF-8', 'UTF-8');


                    if ($textColumns !== null && in_array($index, $textColumns)) {

                        $stringValue = "\t" . $stringValue;
                    }

                    $encodedRow[] = $stringValue;
                }
                fputcsv($output, $encodedRow, ',', '"');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }


    protected function downloadCsvWithPackage(array $headers, array $rows, string $filename)
    {
        $filename = $this->ensureCsvExtension($filename);
        $export = $this->makeCsvExportObject($rows, $headers);

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    protected function downloadStoredCsvIfExists(string $path, string $filename, string $disk = 'public')
    {
        $path = $this->ensureCsvExtension($path);

        $filename = $this->ensureCsvExtension($filename);

        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        if (ob_get_level()) {
            ob_end_clean();
        }
        return response()->download(
            Storage::disk($disk)->path($path),
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    protected function storeAndDownloadCsvWithPackage(
        array $headers,
        array $rows,
        string $filename,
        string $path,
        string $disk = 'public'
    ) {
        $path = $this->ensureCsvExtension($path);
        $filename = $this->ensureCsvExtension($filename);

        if (!Storage::disk($disk)->exists($path)) {
            $export = $this->makeCsvExportObject($rows, $headers);
            Excel::store($export, $path, $disk, \Maatwebsite\Excel\Excel::CSV);
        }

        return response()->download(
            Storage::disk($disk)->path($path),
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    protected function storeAndDownloadCsvByWriter(
        string $filename,
        string $path,
        callable $writer,
        string $disk = 'public'
    ) {
        $path = $this->ensureCsvExtension($path);
        $filename = $this->ensureCsvExtension($filename);
        $storageDisk = Storage::disk($disk);

        if (!$storageDisk->exists($path)) {
            $storageDisk->makeDirectory(dirname($path));
            $handle = fopen($storageDisk->path($path), 'w');

            if ($handle === false) {
                throw new \RuntimeException('Failed to create CSV export file.');
            }

            try {
                $writer($handle);
            } finally {
                fclose($handle);
            }
        }

        return response()->download(
            $storageDisk->path($path),
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    protected function storeAndDownloadChunkedCsv(
        array $headers,
        string $filename,
        string $path,
        callable $rowWriter,
        string $disk = 'public'
    ) {
        return $this->storeAndDownloadCsvByWriter(
            $filename,
            $path,
            function ($handle) use ($headers, $rowWriter) {
                fputcsv($handle, array_values($headers), ',', '"', '\\');
                $rowWriter($handle);
            },
            $disk
        );
    }

    protected function downloadStoredXlsxIfExists(string $path, string $filename, string $disk = 'public')
    {
        $path = preg_replace('/\.[^.]+$/', '', $path) . '.xlsx';
        $filename = preg_replace('/\.[^.]+$/', '', $filename) . '.xlsx';

        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }

        return response()->download(
            Storage::disk($disk)->path($path),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    protected function storeAndDownloadXlsxWithPackage(
        array $headers,
        array $rows,
        string $filename,
        string $path,
        string $disk = 'public'
    ) {
        $path = preg_replace('/\.[^.]+$/', '', $path) . '.xlsx';
        $filename = preg_replace('/\.[^.]+$/', '', $filename) . '.xlsx';

        if (!Storage::disk($disk)->exists($path)) {
            $export = $this->makeCsvExportObject($rows, $headers);
            Excel::store($export, $path, $disk, \Maatwebsite\Excel\Excel::XLSX);
        }

        return response()->download(
            Storage::disk($disk)->path($path),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
