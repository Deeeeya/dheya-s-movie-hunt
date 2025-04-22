<?php

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Load setup file
    require_once '../app/core/setup.php';

    // Initialize router
    $router = new \app\Router();

?>