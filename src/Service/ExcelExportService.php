<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    public function createExcel(array $data): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Valor');

        // Add data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['fecha']);
            $sheet->setCellValue('B' . $row, $item['valor']);
            $row++;
        }

        // Create temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_export');
        $writer->save($tempFile);

        // Get file contents
        $content = file_get_contents($tempFile);

        // Clean up
        unlink($tempFile);

        return $content;
    }
}
