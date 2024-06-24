<?php

use App\FileProcessor;
use PHPUnit\Framework\TestCase;

class FileProcessorTest extends TestCase
{
    public function testProcessFile()
    {
        $fileContent = <<<EOT
Misura------------------------
#Data                        #17-06-2024
#Ora                         #15-07-13
[Unita' di misura            ]M� 
[Misura in unita' elementari-] 7860
[Misura in unita' di misura -] 2.11
[Commento                    ]CEVAHIR
[Intestazione                ]CURTUME TROPICAL
[Scelta manuale              ]2
[Taglia di misura            ]
[Provenienza                 ]Italia
[Spessore--------------------];;
[Selezione                   ]ss1
[Combinazione                ]2
[Combinazione Numerica       ] 2
[Cartone T1 Numero-----------] 1
Misura------------------------
#Data                        #17-06-2024
#Ora                         #15-07-22
[Unita' di misura            ]M� 
[Misura in unita' elementari-] 7925
[Misura in unita' di misura -] 2.12
[Commento                    ]CEVAHIR
[Intestazione                ]CURTUME TROPICAL
[Scelta manuale              ]2
[Taglia di misura            ]
[Provenienza                 ]Italia
[Spessore--------------------];;
[Selezione                   ]ss1
[Combinazione                ]2
[Combinazione Numerica       ] 2
[Cartone T1 Numero-----------] 1
EOT;

        $filePath = tempnam(sys_get_temp_dir(), 'testfile');
        file_put_contents($filePath, $fileContent);

        $fileProcessor = new FileProcessor();
        $result = $fileProcessor->processFile($filePath);

        unlink($filePath);

        $expectedResult = [
            'totalSum' => '4,23',
            'date' => '17-06-2024'
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
