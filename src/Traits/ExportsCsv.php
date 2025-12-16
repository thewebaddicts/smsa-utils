<?php


namespace twa\smsautils\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

trait ExportsCsv
{
    
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
                     
                        if (in_array($index, $this->textColumns)) {
                            $stringValue = (string) $value;
                       
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
                    'use_bom' => true,
                    'delimiter' => ',',
                    'enclosure' => '"',
                    'escape_character' => '"',
                ];
            }
        };

        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }
}