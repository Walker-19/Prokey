<?php 

namespace App\Services;

use App\Services\EncryptionService;
use App\Repositories\PasswordRepository;

class SearchService 
{
    protected $passRepo;

    public function __construct()
    {
        $this->passRepo = new PasswordRepository();
    }

    public function searchPasswords(string $query = '', array $filters = []) 
    {
        $passwords = $this->passRepo->search($query, $filters);

        return $passwords;
    }
}