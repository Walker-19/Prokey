<?php 

namespace App\Console\Makers;

class RepositoryMaker
{
    public static function make(string $name){
        $className = ucfirst($name);
        $repoName = $className . 'Repository';

        $filePath = __DIR__ . '/../../Repositories/' . $repoName . '.php';

        if(file_exists($filePath)){
            echo "Le repository $repoName existe déjà";
            return;
        }


         $content = "<?php

        namespace App\Repositories;

        use App\Models\\{$className};

        class {$repoName} extends BaseRepository
        {
            public function __construct()
            {
                parent::__construct(new {$className}());
            }
        }
        ";

        file_put_contents($filePath, $content);

        echo "Repository créé: app/Repositories/{$repoName}.php";
    }
}