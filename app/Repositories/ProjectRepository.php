<?php

namespace App\Repositories;
use App\Models\Project;
use App\Core\Database;

class ProjectRepository extends BaseRepository
{
    protected $model;

    public function __construct()
    {
        parent::__construct(new Project());
    }

}



