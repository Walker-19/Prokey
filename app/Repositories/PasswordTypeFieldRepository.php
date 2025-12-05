<?php

namespace App\Repositories;
use App\Models\PasswordTypeField;
use App\Core\Database;

class PasswordTypeFieldRepository extends BaseRepository
{
    protected $model;

    public function __construct()
    {
        parent::__construct(new PasswordTypeField());
    }

    public function getFieldsByType($typeId) {
        $sql = "SELECT * FROM password_type_fields WHERE type_id = :type_id ORDER BY sort_order ASC";
        $params = ['type_id' => $typeId];
        return $this->model->query($sql, $params);
    }

}



