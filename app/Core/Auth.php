<?php 

namespace App\Core;

class Auth
{
    public static function check()
    {
        if(empty($_SESSION['user'])) {
            Notification::add('error', 'Vous devez être connecté pour accéder à cette page.');
            header('Location:' . url('/login'));
            exit();
        }
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }
}