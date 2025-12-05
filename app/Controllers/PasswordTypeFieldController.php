<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Notification;
use App\Core\Validator;
use App\Repositories\PasswordTypeFieldRepository;

class PasswordTypeFieldController extends Controller
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new PasswordTypeFieldRepository();
    }

    public function index($password_type_id)
    {
        $password_type = new \App\Repositories\PasswordTypeRepository();
        $type = $password_type->getById($password_type_id);

        $fields = $this->repo->getFieldsByType($password_type_id);

        return $this->view('password_type_field/index', [
            'title' => 'Liste des champs de type de mot de passe : ' . $type['label'],
            'fields' => $fields,
            'password_type_id' => $password_type_id
        ]);
    }

    public function create($password_type_id)
    {
        return $this->view('password_type_field/create', [
            'title' => 'Ajouter un champ au type de mot de passe',
            'password_type_id' => $password_type_id
        ]);
    }
    public function store($password_type_id)
    {   
        
        
        $data = [
            'type_id' => $password_type_id,
            'field_name' => $_POST['field_name'],
            'field_label' => $_POST['field_label'],
            'field_type' => $_POST['field_type']
        ];

        $validated = Validator::make($data, [
            'field_name' => 'required|slug|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number,password,date,email,url'
        ]);

        if (!$validated) {
            Notification::add('error', 'Données invalides. Veuillez vérifier les informations fournies.');
            header('Location: ' . url('/password-types/' . $password_type_id . '/fields/create'));
            exit();
        }

        $last_id = $this->repo->create($data);

        if(!$last_id) {
            Notification::add('error', 'Erreur lors de l\'ajout du champ.');
        }
        Notification::add('sucess', 'Champ ajouté avec succès.');

        header('Location: ' . url('/password-types/' . $password_type_id . '/fields'));
        exit();
    }

    function edit($password_type_id, $id)
    {
        $field = $this->repo->getById($id);
        $password_type = new \App\Repositories\PasswordTypeRepository();
        $type = $password_type->getById($password_type_id);

        if (!$field) {
            return $this->view('errors/404', ['title' => 'Champ non trouvé']);
        }

        return $this->view('password_type_field/edit', [
            'title' => 'Éditer le champ : ' . $field['field_label'] . ' du type de mot de passe : ' . $type['label'],
            'field' => $field,
            'password_type_id' => $password_type_id
        ]);
    }

    function update($password_type_id, $id)
    {
        $data = [
            'field_name' => $_POST['field_name'],
            'field_label' => $_POST['field_label'],
            'field_type' => $_POST['field_type']
        ];

        $this->repo->update($id, $data);

        header('Location: ' . url('/password-types/' . $password_type_id . '/fields'));
        exit();
    }

    function destroy($password_type_id, $id)
    {
        $this->repo->delete($id);

        header('Location: ' . url('/password-types/' . $password_type_id . '/fields'));
        exit();
    }

    public function getFieldsJson($password_type_id)
    {
        $fields = $this->repo->getFieldsByType($password_type_id);
        
        // Retourner une table HTML
        $html = '<table><tbody>';
        
        foreach ($fields as $field) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($field['field_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($field['field_label']) . '</td>';
            $html .= '<td>' . htmlspecialchars($field['field_type']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

}