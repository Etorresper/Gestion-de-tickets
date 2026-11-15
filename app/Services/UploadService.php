<?php
namespace App\Services;

class UploadService {
    private $uploadDir = '../public/uploads/';
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    private $maxSize = 5242880; // 5MB

    public function __construct() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => 'Error al subir el archivo.'];
        }

        if ($file['size'] > $this->maxSize) {
            return ['status' => 'error', 'message' => 'El archivo excede el tamaño máximo permitido (5MB).'];
        }

        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['status' => 'error', 'message' => 'Tipo de archivo no permitido.'];
        }

        $filename = time() . '_' . basename($file['name']);
        $targetPath = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'status' => 'success',
                'name' => $file['name'],
                'path' => 'uploads/' . $filename,
                'mime' => $file['type']
            ];
        }

        return ['status' => 'error', 'message' => 'No se pudo guardar el archivo en el servidor.'];
    }
}
