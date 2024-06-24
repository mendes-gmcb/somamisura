<?php

use App\CsvCreator;
use PHPUnit\Framework\TestCase;

class CsvCreatorTest extends TestCase
{
    public function testCreateCsv()
    {
        $data = [
            ['filename' => 'file1.txt', 'date' => '17-06-2024', 'sum' => '2,11'],
            ['filename' => 'file2.txt', 'date' => '17-06-2024', 'sum' => '2,12']
        ];

        $csvCreator = new CsvCreator();
        $filePath = tempnam(sys_get_temp_dir(), 'csv');
        
        $csvCreator->createCsv($filePath, $data);

        $expectedContent = <<<EOT
Arquivo,Data,Soma
file1.txt,17-06-2024,"2,11"
file2.txt,17-06-2024,"2,12"

EOT;

        $actualContent = file_get_contents($filePath);

        unlink($filePath);

        $this->assertEquals($expectedContent, $actualContent);
    }
}
?>
