<?php

namespace App\Repositories;

use App\Models\User;
use App\Core\Database;

class UserRepository extends BaseRepository
{
    protected $model;

    public function __construct()
    {
        parent::__construct(new User());
    }

    public function findByEmail($email)
    {
        $result = $this->model->query('SELECT * FROM users WHERE email = ?', [$email]);
        var_dump($result);
        return $result[0] ?? null;
    }

}



