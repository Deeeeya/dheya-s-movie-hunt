<?php

    namespace app\controllers;

    class Controller {
        
        protected function view($view) {
            $view_file = "../public/assets/views/" . $view . ".html";
            if(file_exists($view_file)) {
                require $view_file;
                exit();
            } else {
                echo "View not found: " . $view_file;
                exit();
            }
        }
        
        protected function json($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }
    }

?>