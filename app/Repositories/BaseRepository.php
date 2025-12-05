<?php
namespace App\Repositories;

use App\Core\Model;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->getAll();
    }
    public function getAllWhere($column, $value) {
        return $this->model->getAllWhere($column, $value);
    }
    public function getById($id)
    {
        return $this->model->getById($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }   

    public function delete($id)
    {
        return $this->model->delete($id);
    }
}