<?php

namespace App\Controllers;

use App\Core\Controller;

use App\Repositories\ProjectRepository;
use App\Repositories\PasswordTypeRepository;
use App\Services\SearchService;

class SearchController extends Controller
{
    protected $projectRepo;
    protected $typeRepo;
    protected $searchService;

    public function __construct()
    {
        $this->projectRepo = new ProjectRepository();
        $this->typeRepo = new PasswordTypeRepository();
        $this->searchService = new SearchService();
    }

    function index() {
        $query = $_GET["s"] ?? '';
        $projectId = $_GET["project_id"] ?? null;
        $typeId = $_GET["password_type_id"] ?? null;

        $projects = $this->projectRepo->getAll();
        $types = $this->typeRepo->getAll();


        //Utiliser le searchService pour faire la recherche 
        $results = $this->searchService->searchPasswords($query, [
            'password_type_id' => $typeId,
            'project_id' => $projectId,
        ]);
    

        return $this->view('search/index', [
            'title' => 'Recherche de mot de passe',
            's'     => $query,
            'project_id' => $projectId,
            'type_id' => $typeId,
            'project_id' => $projectId,
            'projects' => $projects,
            'results' => $results,
            'types' => $types,

        ]);
    }
}