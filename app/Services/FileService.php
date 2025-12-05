<?php
    namespace App\Services;
    
    use App\Services\EncryptionService;

class FileService
{
    protected $encryptionService;
    protected $storagePath = __DIR__ . '/../../storage/files/';
    public function __construct()
    {
        $this->encryptionService = new EncryptionService();
        if(!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }


    public function uploadFiles(array $files) {
        $uploadedFilePaths = [];

        foreach ($files['tmp_name'] as $index => $tmpName) {
            $encrypted = 0;
            if(is_uploaded_file($tmpName)) {
                $fileName = basename($files['name'][$index]);
                $storedName = uniqid() . '_' . $fileName;
                $mimeType = mime_content_type($tmpName);
                $size = filesize($tmpName);
                $targetPath = $this->storagePath . $storedName;
                if(move_uploaded_file($tmpName, $targetPath)) {
                   // Lire le contenu du fichier en binaire
                   $fileContent = file_get_contents($targetPath);
                   // Chiffrer le contenu du fichier
                   $encryptedContent = $this->encryptionService->encrypt($fileContent);
                   if($encryptedContent) {
                       $encrypted = 1;
                   }
                   file_put_contents($targetPath, $encryptedContent);

                     $uploadedFilePaths[] = [
                        'filename' => $fileName,
                        'stored_name' => $storedName,
                        'mime_type' => $mimeType,
                        'size' => $size,
                        'encrypted' => $encrypted,
                     ];
                }
            }
        }
        return $uploadedFilePaths;
    }

    public function downloadFile($fileName) {
        $filePath = $this->storagePath . $fileName;
        if(!file_exists($filePath)) {
            return null;
        }
        $encryptedContent = file_get_contents($filePath);
        $decryptedContent = $this->encryptionService->decrypt($encryptedContent);
        return $decryptedContent;
    }

    public function deleteFile($fileName) {
        $filePath = $this->storagePath . $fileName;
        if(file_exists($filePath)) {
            unlink($filePath);
            return true;
        }
        return false;
    }

}