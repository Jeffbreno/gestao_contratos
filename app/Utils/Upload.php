<?php

namespace App\Utils;


use App\Controller\Admin\AlertController;
use Exception;

class Upload
{
    private string $uploadDir;
    private array $allowedExtensions;
    private float $maxFileSize;

    public function __construct($uploadDir, $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'), $maxFileSize = 25)
    {
        $this->uploadDir = $uploadDir;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxFileSize = $maxFileSize;
    }

    public function upload(array $file): string
    {
        // Verifica se houve algum erro no upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erro no upload do arquivo.');
        }

        // Obtém informações sobre o arquivo
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];

        // Verifica a extensão do arquivo
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $this->allowedExtensions)) {
            return AlertController::getError('Extensão de arquivo não permitida.');
        }

        // Verifica o tamanho do arquivo
        if ($fileSize > $this->maxFileSize) {
            return AlertController::getError('Tamanho de arquivo excede o limite permitido.');
        }

        // Gera um nome único para o arquivo e move para o diretório de upload
        $uniqueFileName = uniqid() . '.' . $fileExtension;
        $destination = $this->uploadDir . DIRECTORY_SEPARATOR . $uniqueFileName;
        if (!move_uploaded_file($fileTmp, $destination)) {
            return AlertController::getError('Falha ao mover o arquivo para o diretório de upload.');
        }

        return $uniqueFileName;
    }
}
