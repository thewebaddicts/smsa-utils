<?php


namespace twa\smsautils\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

trait ExportsCsv
{
    /**
     * Reusable method to download CSV files with proper formatting (Native PHP)
     * 
     * @param array $headers Column headers
     * @param array $rows Array of data rows (each row is an array)
     * @param string $filename Filename for the download
     * @param array|null $textColumns Array of column indices (0-based) that should be treated as text to prevent scientific notation
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function downloadCsv(array $headers, array $rows, string $filename, ?array $textColumns = null)
    {
        return response()->streamDownload(function () use ($headers, $rows, $textColumns) {
            // Add UTF-8 BOM for Excel compatibility (especially for Arabic text)
            // This must be the first thing written to ensure proper encoding
            echo "\xEF\xBB\xBF";
            
            // Open output stream with UTF-8 encoding
            $output = fopen('php://output', 'w');
            
            // Write headers with proper encoding
            $encodedHeaders = array_map(function ($header) {
                return mb_convert_encoding($header, 'UTF-8', 'UTF-8');
            }, $headers);
            fputcsv($output, $encodedHeaders, ',', '"');
            
            // Write data rows
            foreach ($rows as $row) {
                $encodedRow = [];
                foreach ($row as $index => $value) {
                    // Convert to string and ensure UTF-8 encoding
                    $stringValue = (string) $value;
                    $stringValue = mb_convert_encoding($stringValue, 'UTF-8', 'UTF-8');
                    
                    // If this column should be treated as text (to prevent scientific notation),
                    // prefix with tab character which forces Excel to treat it as text
                    if ($textColumns !== null && in_array($index, $textColumns)) {
                        // Prefix with tab to force Excel to treat as text
                        // This prevents scientific notation for large numbers
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

    /**
     * Reusable method to download CSV files using Excel package (Better for Arabic text and number formatting)
     * Use this when you need better control over text formatting and Arabic text display
     * 
     * @param array $headers Column headers
     * @param array $rows Array of data rows (each row is an array)
     * @param string $filename Filename for the download
     * @param array|null $textColumns Array of column indices (0-based) that should be treated as text
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function downloadCsvWithPackage(array $headers, array $rows, string $filename, ?array $textColumns = null)
    {
        $export = new class($rows, $headers, $textColumns) implements FromArray, WithHeadings, WithCustomCsvSettings {
            protected $data;
            protected $headings;
            protected $textColumns;

            public function __construct($data, $headings, $textColumns) {
                $this->data = $data;
                $this->headings = $headings;
                $this->textColumns = $textColumns;
            }

            public function array(): array {
                // Process rows to ensure text columns are properly formatted
                return array_map(function ($row) {
                    if ($this->textColumns === null) {
                        return $row;
                    }
                    $processedRow = [];
                    foreach ($row as $index => $value) {
                        // For text columns, use equals sign formula to force Excel to treat as text
                        // This prevents scientific notation (e.g., 2.9153E+11) for large numbers
                        // Format: ="291525701009" forces Excel to display as text
                        if (in_array($index, $this->textColumns)) {
                            $stringValue = (string) $value;
                            // Use equals sign formula to force text format in Excel
                            // This is the most reliable method for CSV files
                            $processedRow[] = '="' . str_replace('"', '""', $stringValue) . '"';
                        } else {
                            $processedRow[] = $value;
                        }
                    }
                    return $processedRow;
                }, $this->data);
            }

            public function headings(): array {
                return $this->headings;
            }

            public function getCsvSettings(): array {
                return [
                    'use_bom' => true, // UTF-8 BOM for Arabic text
                    'delimiter' => ',',
                    'enclosure' => '"',
                    'escape_character' => '"',
                ];
            }
        };

        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }
}