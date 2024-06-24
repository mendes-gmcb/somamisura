<?php
namespace App;

class FileProcessor {
    public function processFile($filePath) {
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
}
?>
