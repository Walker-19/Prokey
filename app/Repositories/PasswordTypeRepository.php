<?php

namespace App\Repositories;
use App\Models\PasswordType;
use App\Core\Database;

class PasswordTypeRepository extends BaseRepository
{
    protected $model;

    public function __construct()
    {
        parent::__construct(new PasswordType());
    } 
}



