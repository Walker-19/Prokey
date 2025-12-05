<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Notification;
use App\Core\Validator;
use App\Repositories\PasswordRepository;
use App\Repositories\PasswordTypeRepository;
use App\Repositories\PasswordTypeFieldRepository;
use App\Services\EncryptionService;
use App\Services\FileService;
use App\Repositories\FileRepository;

class PasswordController extends Controller
{
    protected $passwordRepository;
    protected $fileService;
    protected $fileRepository;
    public function __construct()
    {
        $this->passwordRepository = new PasswordRepository();
        $this->fileService = new FileService();
        $this->fileRepository = new FileRepository();
    }

    public function create($project_id)
    {       
        $passwordTypeRepo = new PasswordTypeRepository();
        $password_types = $passwordTypeRepo->getAll();

        $selected_type = $_GET['password_type_id'] ?? null;

        $fields = [];
        if ($selected_type) {
            $passwordTypeFieldRepo = new PasswordTypeFieldRepository();
            $fields = $passwordTypeFieldRepo->getFieldsByType($selected_type);
        }

        $this->view('password/create', [
             'title' => 'Ajouter un mot de passe',
             'project_id' => $project_id,
             'selected_type' => $selected_type,
             'password_types' => $password_types,
             'fields' => $fields,
            ]
        );
    }

    public function store()
    {   
        
        $validated = Validator::make($_POST, [
            'project_id' => 'required|number',
            'password_type_id' => 'required|number',
            'label' => 'required|string|max:255',
            'extra' => 'required'
        ]);
        
     
        if (!$validated) {
            Notification::add('error', 'Données invalides. Veuillez vérifier les informations fournies.');
            header('Location: ' . url('/projects/' . $_POST['project_id'] . '/show'));
            exit();
        }

        $extra = $_POST['extra'];
        $extraJSON = json_encode($extra);
       
        $extraEncrypted = EncryptionService::encrypt($extraJSON);
        
        $data = [
            'project_id' => $_POST['project_id'],
            'type_id' => $_POST['password_type_id'],
            'label' => $_POST['label'],
            'extra' => $extraEncrypted, 
        ];


       $passwordProjectId =  $this->passwordRepository->create($data);
        $files = $_FILES['files'] ?? null;
         if($files) {
         $result = $this->fileService->uploadFiles($files);
            foreach($result as $fileData) {
                $data = [
                    'password_id' => $passwordProjectId, 
                    'filename' => $fileData['filename'],
                    'stored_name' => $fileData['stored_name'],
                    'size' => $fileData['size'],
                    'mime_type' => $fileData['mime_type'],
                    'encrypted' => $fileData['encrypted'],
                ];
                $this->fileRepository->create($data);
            }
         }

        header('Location: ' . url('/projects/' . $_POST['project_id'] . '/show'));
        exit();
    }

       public function edit($project_id, $id)
    {
        $password = $this->passwordRepository->getById($id);
        if (!$password) {
            $this->view('errors/404', ['title' => 'Mot de passe non trouvé']);
            return; 
        }

        $passwordTypeRepo = new PasswordTypeRepository();
        $password_types = $passwordTypeRepo->getAll();

        $passwordTypeFieldRepo = new PasswordTypeFieldRepository();
        $fields = $passwordTypeFieldRepo->getFieldsByType($password['type_id']);

        $extra = json_decode($password['extra'], true) ?? [];

        $this->view('password/edit', [
             'title' => 'Editer le mot de passe',
             'project_id' => $project_id,
             'password' => $password,
             'password_types' => $password_types,
             'fields' => $fields,
             'extra' => $extra,
            ]
        );
    }
    
    public function update($project_id, $id)
    {
        $password = $this->passwordRepository->getById($id);
        if (!$password) {
            $this->view('errors/404', ['title' => 'Mot de passe non trouvé']);
            return; 
        }

        // Validation des données
        $validated = Validator::make($_POST, [
            'password_type_id' => 'required|number',
            'label' => 'required|string|max:255',
            'extra' => 'required'
        ]);

        if (!$validated) {
            Notification::add('error', 'Données invalides. Veuillez vérifier les informations fournies.');
            header('Location: ' . url('/projects/' . $project_id . '/show'));
            exit();
        }

        // Récupérer les données anciennes
        $oldExtra = is_array($password['extra']) ? $password['extra'] : (json_decode($password['extra'], true) ?? []);
        
        // Si extra est chiffré, le déchiffrer
        if (is_string($password['extra'])) {
            try {
                $decrypted = EncryptionService::decrypt($password['extra']);
                $oldExtra = json_decode($decrypted, true) ?? [];
            } catch (\Exception $e) {
                $oldExtra = [];
            }
        }

        // Récupérer les nouvelles données
        $newExtra = $_POST['extra'] ?? [];
        $newLabel = $_POST['label'];
        $newTypeId = $_POST['password_type_id'];

        // Fusionner : garder les anciennes valeurs et remplacer par les nouvelles si elles ne sont pas vides
        $mergedExtra = $oldExtra;
        foreach ($newExtra as $key => $value) {
            // Ne mettre à jour que les champs non vides
            if (!empty($value)) {
                $mergedExtra[$key] = $value;
            }
        }

        // Déterminer quels champs ont été modifiés
        $hasChanges = ($password['label'] != $newLabel) || 
                     ($password['type_id'] != $newTypeId) || 
                     ($oldExtra != $mergedExtra);

        if (!$hasChanges && empty($_FILES['files']['tmp_name'][0])) {
            Notification::add('info', 'Aucun changement détecté.');
            header('Location: ' . url('/projects/' . $project_id . '/show'));
            exit();
        }

        // Mettre à jour le mot de passe
        $extraJSON = json_encode($mergedExtra);
        $extraEncrypted = EncryptionService::encrypt($extraJSON);

        $data = [
            'type_id' => $newTypeId,
            'label' => $newLabel,
            'extra' => $extraEncrypted,
        ];

        $this->passwordRepository->update($id, $data);

        // Gérer les fichiers uploadés s'il y en a
        $files = $_FILES['files'] ?? null;
        if ($files && !empty($files['tmp_name'][0])) {
            $result = $this->fileService->uploadFiles($files);
            foreach ($result as $fileData) {
                $fileRecord = [
                    'password_id' => $id,
                    'filename' => $fileData['filename'],
                    'stored_name' => $fileData['stored_name'],
                    'size' => $fileData['size'],
                    'mime_type' => $fileData['mime_type'],
                    'encrypted' => $fileData['encrypted'],
                ];
                $this->fileRepository->create($fileRecord);
            }
            Notification::add('success', 'Mot de passe et fichiers mis à jour avec succès.');
        } else {
            Notification::add('success', 'Mot de passe mis à jour avec succès.');
        }

        header('Location: ' . url('/projects/' . $project_id . '/show'));
        exit();
    }

    public function destroy($project_id, $id)
    {
        $password = $this->passwordRepository->getById($id);
        if (!$password) {
            $this->view('errors/404', ['title' => 'Mot de passe non trouvé']);
            return; 
        }

        $this->passwordRepository->delete($id);

        header('Location: ' . url('/projects/' . $project_id . '/show'));
        exit();
    }

}