<?php

namespace App\Repositories;

use App\Models\File;
use App\Services\FileService;

class FileRepository extends BaseRepository
{
        protected $fileService;
        public function __construct()
        {
                parent::__construct(new File());
                $this->fileService = new FileService();
        }


    public function allByPasswordId($passwordId)
    {
        $sql = "SELECT * FROM files WHERE password_id = ?";
        $param = [$passwordId];
        $files = $this->model->query($sql, $param);
        return $files;
    }

    public function deleteFilesById($fileId)
    {
        $file = $this->getFileById($fileId);
        if ($file) {
            // Supprimer le fichier du système de fichiers
            $this->fileService->deleteFile($file['stored_name']);

            // Supprimer l'enregistrement de la base de données
            $this->model->query("DELETE FROM files WHERE id = ?", [$fileId]);
            return true;
        }
        return false;
    }

    public function getFileById($fileId) {
        $sql = "SELECT * FROM files WHERE id = ?";
        $param = [$fileId];
        $file = $this->model->query($sql, $param);
        return $file ? $file[0] : null;
    }

    public function downloadFile($fileId) {
        $file = $this->getFileById($fileId);
        $fileUpload = $this->fileService->downloadFile($file['stored_name']);
        return $fileUpload;
    }


}
        