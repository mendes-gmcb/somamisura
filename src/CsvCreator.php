<?php
namespace App;

class CsvCreator {
    public function createCsv($filePath, $data) {
        $file = fopen($filePath, 'w');

        fputcsv($file, ['Arquivo', 'Data', 'Soma']);

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }
}
?>
