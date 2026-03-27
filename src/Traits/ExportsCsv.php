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


    protected function downloadCsvWithPackage(array $headers, array $rows, string $filename)
    {
        $export = new class($rows, $headers) implements \Maatwebsite\Excel\Concerns\FromArray,
                                                        \Maatwebsite\Excel\Concerns\WithHeadings,
                                                        \Maatwebsite\Excel\Concerns\WithCustomCsvSettings {
            protected $data;
            protected $headings;
    
            public function __construct($data, $headings) {
                $this->data = $data;
                $this->headings = $headings;
            }
    
            public function array(): array {
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
    
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }
}