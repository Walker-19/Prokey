<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Notification;
use App\Core\Validator;
use App\Repositories\ProjectRepository;
use App\Repositories\PasswordRepository;
use App\Repositories\FileRepository;
use App\Services\PasswordService;
class ProjectController extends Controller
{
    protected $projectRepository;

    public function __construct()
    {
        $this->projectRepository = new ProjectRepository();
    }

    public function index()
    {
        $column = "user_id";
        $value = $_SESSION['user']['id'];
        $projects = $this->projectRepository->getAllWhere($column, $value);
        $this->view('project/index', ['projects' => $projects]);
    }

    public function create()
    {   
        $this->view('project/create', ['title' => 'Créer un projet']);
    }

    public function store()
    {   
        $validated = Validator::make($_POST, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000'
        ]);

        if (!$validated) {
            Notification::add('error', 'Données invalides. Veuillez vérifier les informations fournies.');
            header('Location: ' . url('/projects'));
            exit();
        }
        // Associer le projet à l'utilisateur connecté
        $_POST['user_id'] = $_SESSION['user']['id'];
        $this->projectRepository->create($_POST);
        header('Location: ' . url('/projects'));
        exit();
    }

    public function show($id)
    {
        $project = $this->projectRepository->getById($id);
        $filesPassword = [];
        $file_repository = new FileRepository();
        if(!$project) {
            return $this->view('errors/404', ['title' => 'Projet non trouvé']);
        }

        $passwordRepository = new PasswordRepository();
        $passwords = $passwordRepository->allByProjectId($id);
        $passwordService = new PasswordService();
        
        foreach ($passwords as &$password) {
            $filesPassword[$password['id']] = $file_repository->allByPasswordId($password['id']);
            
            // Calculer la force du mot de passe si possible
            $extra = is_array($password['extra']) ? $password['extra'] : (json_decode($password['extra'], true) ?? []);
            $passwordScore = null;
            
            // Chercher un champ de type password dans extra
            foreach ($extra as $key => $value) {
                // Supposons que la clé contient 'password' ou 'pwd'
                if (stripos($key, 'password') !== false || stripos($key, 'pwd') !== false) {
                    $result = $passwordService->password_strength_score($value);
                    $passwordScore = $result;
                    break;
                }
            }
            
            $password['strength'] = $passwordScore;
        }

        $this->view('project/show', [
            'title'=> $project['name'], 
            'project_id'=> $project['id'], 
            'project' => $project,
            'passwords' => $passwords,
            'filesPassword' => $filesPassword
        ]);
    }

    public function edit($id)
    {
        $project = $this->projectRepository->getById($id);

        if(!$project) {
            return $this->view('errors/404', ['title' => 'Projet non trouvé']);
        }

        $this->view('project/edit', [
            'title'=> 'Éditer le projet', 
            'project' => $project
        ]);
    }

    public function update($id)
    {
        $validated = Validator::make($_POST, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000'
        ]);

        if (!$validated) {
            Notification::add('error', 'Données invalides. Veuillez vérifier les informations fournies.');
            header('Location: ' . url('/projects/' . $id . '/show'));
            exit();
        }

        $this->projectRepository->update($id, $_POST);

        header('Location: ' . url('/projects/' . $id . '/show'));
        exit();
    }

    public function destroy($id)
    {
        $this->projectRepository->delete($id);

        header('Location: ' . url('/projects'));
        exit();
    }


}