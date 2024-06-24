<?php

namespace App;

require "FileProcessor.php";
require "CsvCreator.php";

use App\FileProcessor;
use App\CsvCreator;

class UploadHandler {
    private $fileProcessor;
    private $csvCreator;

    public function __construct($fileProcessor = null, $csvCreator = null) {
        $this->fileProcessor = $fileProcessor ?: new FileProcessor();
        $this->csvCreator = $csvCreator ?: new CsvCreator();
    }

    public function handleUpload($files) {
        $uploads_dir = __DIR__ . '/../uploads';
        $results = [];

        foreach ($files['tmp_name'] as $index => $tmpName) {
            $originalName = $files['name'][$index];
            $destination = $uploads_dir . '/' . basename($originalName);

            if (move_uploaded_file($tmpName, $destination)) {
                $result = $this->fileProcessor->processFile($destination);
                $results[] = [
                    'filename' => $originalName,
                    'date' => $result['date'],
                    'sum' => $result['totalSum']
                ];
                // unlink($destination); // Exclui o arquivo original
            }
        }

        $csvFile = $uploads_dir . '/results.csv';
        $this->csvCreator->createCsv($csvFile, $results);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="results.csv"');
        header('Pragma: no-cache');
        readfile($csvFile);
        // unlink($csvFile); // Excluir o arquivo CSV após o download
    }
}
?>
