<?php

require 'src/UploadHandler.php';

use App\UploadHandler;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $uploadHandler = new UploadHandler();
    $uploadHandler->handleUpload($_FILES['files']);
}
?>
