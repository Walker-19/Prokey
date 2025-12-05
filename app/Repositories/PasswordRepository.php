<?php

namespace App\Repositories;
use App\Models\Password;
use App\Core\Database;
use App\Services\EncryptionService;

class PasswordRepository extends BaseRepository
{
    protected $model;

    public function __construct()
    {
        parent::__construct(new Password());
    }

    public function allByProjectId($projectId)
    {
        $passwords = $this->model->query("SELECT p.*, t.label AS type_label, t.color AS type_color
                             FROM passwords p
                             JOIN password_types t ON p.type_id = t.id
                             WHERE p.project_id = ?", [$projectId]);

        foreach ($passwords as &$password) {
            $password['extra'] = EncryptionService::decrypt($password['extra']);
            $password['extra'] = json_decode($password['extra'], true);
        }
        return $passwords;                      
    }

    public function search(string $query, array $filtres) {
        $sql = "SELECT p.*, t.label as type_label, t.color as type_color, pr.name as project_name
         FROM passwords p 
         JOIN password_types t ON p.type_id = t.id 
         JOIN projects pr ON p.project_id = pr.id
         WHERE 1 ";

        $params = [];

        if(!empty($query)) {
            $sql .= "AND p.label LIKE ? ";
            $params[] = "%$query%";
        }

        if(!empty($filters['project_id'])){
            $sql .= "AND p.project_id = ? ";
            $params[] = $filters['project_id'];
        }

        if(!empty($filters['password_type_id'])){
            $sql .= "AND p.type_id = ? ";
            $params[] = $filters['password_type_id'];
        }

        $sql .= " ORDER BY p.created_at DESC";

        $passwords = $this->model->query($sql, $params);
       
        foreach ($passwords as &$password) {
            $password['extra'] = EncryptionService::decrypt($password['extra']);
            echo  $password['extra'];
            $password['extra'] = json_decode($password['extra'], true);
        }
        return $passwords; 

    }

    

}



