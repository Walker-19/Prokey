<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Core\Notification;
use App\Repositories\FileRepository;
use App\Services\FileService;
class FileController extends Controller
{
    protected $fileRepository;
    public function __construct()
    {
        $this->fileRepository = new FileRepository();
    }


    public function download($fileid) {
        // Récupérer les infos du fichier
        $file = $this->fileRepository->getFileById($fileid);
        
        if (!$file) {
            http_response_code(404);
            echo "Fichier non trouvé";
            return;
        }

        // Déchiffrer et récupérer le contenu du fichier
        $decryptedContent = $this->fileRepository->downloadFile($fileid);
        
        if ($decryptedContent === null) {
            http_response_code(404);
            echo "Impossible de télécharger le fichier";
            return;
        }

        // Envoyer les headers pour forcer le téléchargement
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
        header('Content-Length: ' . strlen($decryptedContent));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Envoyer le contenu du fichier
        echo $decryptedContent;
        exit();
    }

    public function delete($fileId) {
        // Récupérer le fichier pour connaître le password_id et le project_id
        $file = $this->fileRepository->getFileById($fileId);
        
        if (!$file) {
            Notification::add('error', 'Fichier non trouvé.');
            header('Location: ' . url('/projects'));
            exit();
        }
        
        $passwordId = $file['password_id'];
        
        // Supprimer le fichier de la base de données
        $deleted = $this->fileRepository->deleteFilesById($fileId);
        
        if ($deleted) {
            // Supprimer aussi le fichier du disque
            $storagePath = __DIR__ . '/../../storage/files/' . $file['stored_name'];
            if (file_exists($storagePath)) {
                unlink($storagePath);
            }
            Notification::add('success', 'Fichier supprimé avec succès.');
        } else {
            Notification::add('error', 'Erreur lors de la suppression du fichier.');
        }
        
        // Rediriger vers la page du projet
        $passwordRepo = new \App\Repositories\PasswordRepository();
        $password = $passwordRepo->getById($passwordId);
        if ($password) {
            header('Location: ' . url('/projects/' . $password['project_id'] . '/show'));
        } else {
            header('Location: ' . url('/projects'));
        }
        exit();
    }
}