<?php 

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Console\Makers\ModelMaker;
use App\Console\Makers\RepositoryMaker;

$type = $argv[1] ?? null;
$name = $argv[2] ?? null;

if(!$type || !$name) {
    echo "verifier la syntaxe : composer make:[type] [name]";
    exit;
}

switch ($type) {
    case 'model':
        ModelMaker::make($name);
        break;

    case 'repository':
        RepositoryMaker::make($name);
        break;
    
    default:
        echo "Commande inconnue";
        break;
}
