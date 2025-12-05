<?php 

namespace App\Core;
class Controller {
   
    public function view($viewName, $data = []) {
        return View::render($viewName, $data);  
    }
}