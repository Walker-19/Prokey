<?php 

namespace App\Controllers;
use App\Core\Controller;

class HomeController extends Controller {

    public function index() {
        $data = ['title' => 'Welcome to Pro-Key'];
        return $this->view('home/index' ,$data);
    }
}