<?php 

namespace App\Console\Makers;

class ModelMaker
{
    public static function make(string $name){
        $className = ucfirst($name);
        $filePath = __DIR__ . '/../../Models/' . $className . '.php';

        if(file_exists($filePath)){
            echo "Le model $className existe déjà";
            return;
        }

        $table = strtolower($name) . 's';

        $content = "<?php

        namespace App\Models;

        use App\Core\Model;

        class {$className} extends Model
        {
            protected \$table = '{$table}';
        }
        ";

        file_put_contents($filePath, $content);

        echo "Model créé: app/Models/{$className}.php";
    }
}