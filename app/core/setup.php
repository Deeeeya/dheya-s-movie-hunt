<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    define('ROOT_PATH', dirname(__DIR__, 2));
    define('APP_PATH', ROOT_PATH . '/app');
    define('CONTROLLER_PATH', APP_PATH . '/controllers');
    define('MODEL_PATH', APP_PATH . '/models');
    define('PUBLIC_PATH', ROOT_PATH . '/public');

    spl_autoload_register(function($className) {
        $className = str_replace('\\', '/', $className);
        $filePath = ROOT_PATH . '/' . $className . '.php';
        
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    });

    $envFile = ROOT_PATH . '/.env';
    if (file_exists($envFile)) {
        $env = parse_ini_file($envFile);
        
        define('DBHOST', $env['DBHOST'] ?? 'localhost');
        define('DBNAME', $env['DBNAME'] ?? 'movie_watchlist');
        define('DBUSER', $env['DBUSER'] ?? 'root');
        define('DBPASS', $env['DBPASS'] ?? '');
        define('TMDB_API_KEY', $env['TMDB_API_KEY'] ?? '');
    } else {
        die('.env file not found');
    }

    require_once CONTROLLER_PATH . '/Controller.php';
    require_once CONTROLLER_PATH . '/MainController.php';
    require_once CONTROLLER_PATH . '/MovieController.php';
    require_once CONTROLLER_PATH . '/ApiController.php';
    require_once MODEL_PATH . '/Model.php';
    require_once MODEL_PATH . '/Movie.php';
    require_once MODEL_PATH . '/Review.php';
    require_once APP_PATH . '/Router.php';

?>