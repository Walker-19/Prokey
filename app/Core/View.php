<?php

namespace App\Core;
use App\Core\Notification;
use App\Core\Auth;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use App\Repositories\PasswordTypeRepository;

class View {

    private static $twig = null;
    private static function init() {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../Views');
            self::$twig = new Environment($loader, [
                'cache' => false,
                'auto_reload' => true,
                'debug' => true,
            ]);

            self::$twig->addGlobal('auth_user', Auth::user());
            self::$twig->addGlobal('notifications', Notification::get());
            
            // Ajouter les types de mots de passe comme variable globale
            try {
                $passwordTypeRepo = new PasswordTypeRepository();
                $password_types = $passwordTypeRepo->getAll();
                self::$twig->addGlobal('password_types', $password_types);
            } catch (\Exception $e) {
                self::$twig->addGlobal('password_types', []);
            }
            
            self::$twig->addExtension(new \Twig\Extension\DebugExtension());
            self::$twig->addFunction(new TwigFunction('url', function ($path = '') {
                return url($path);
            }));
        }
    }

    public static function render(string $viewName, array $data = []) {
        self::init();
        echo self::$twig->render($viewName . '.twig', $data);
    }
}