<?php

    namespace app\controllers;

    class MainController extends Controller {
        
        public function index() {
            header('Location: /movies');
            exit();
        }
        
        public function notFound() {
            echo "404 - Page not found";
            exit();
        }
    }

?>