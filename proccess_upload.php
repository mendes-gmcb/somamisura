<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $uploads_dir = __DIR__ . '/uploads';
    $results = [];

    foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
        $originalName = $_FILES['files']['name'][$index];
        $destination = $uploads_dir . '/' . basename($originalName);

        if (move_uploaded_file($tmpName, $destination)) {
            $result = sumMizura($destination);
            $results[] = [
                'filename' => $originalName,
                'date' => $result['date'],
                'sum' => $result['totalSum']
            ];
            unlink($destination); // Exclui o arquivo original
        }
    }

    $csvFile = $uploads_dir . '/results.csv';
    createCsv($csvFile, $results);

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="results.csv"');
    header('Pragma: no-cache');
    readfile($csvFile);
    unlink($csvFile); // Excluir o arquivo CSV após o download
}

function sumMizura($filePath) {
    $file = fopen($filePath, 'r');
    $totalSum = 0;
    $date = '';

    while (($line = fgets($file)) !== false) {
        if (strpos($line, "[Misura in unita' di misura -]") !== false) {
            preg_match("/\[Misura in unita' di misura -\]\s+([\d.]+)/", $line, $matches);
            if (!empty($matches[1])) {
                $value = (float) $matches[1];
                $totalSum += $value;
            }
        }
        if (strpos($line, '#Data') !== false) {
            preg_match("/#Data\s+#(\d{2}-\d{2}-\d{4})/", $line, $dateMatches);
            if (!empty($dateMatches[1])) {
                $date = $dateMatches[1];
            }
        }
    }

    fclose($file);

    $totalSum = number_format($totalSum, 2, ',', '');

    return ['totalSum' => $totalSum, 'date' => $date];
}

function createCsv($filePath, $data) {
    $file = fopen($filePath, 'w');

    fputcsv($file, ['Arquivo', 'Data', 'Soma']);

    foreach ($data as $row) {
        fputcsv($file, $row);
    }

    fclose($file);
}
?>
