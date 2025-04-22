<?php

    namespace app;

    use app\controllers\MainController;
    use app\controllers\MovieController;
    use app\controllers\ApiController;

    class Router {
        
        private $routes = [];
        
        public function __construct() {
            // Define routes
            $this->routes = [
                'GET' => [
                    '/' => [MainController::class, 'index'],
                    '/movies' => [MovieController::class, 'browse'],
                    '/movie' => [MovieController::class, 'detail'],
                    '/api/movies' => [ApiController::class, 'getMovies'],
                    '/api/movie' => [ApiController::class, 'getMovieDetails'],
                    '/api/reviews' => [MovieController::class, 'getReviews']
                ],
                'POST' => [
                    '/api/reviews' => [MovieController::class, 'addReview']
                ]
            ];
            
            // Route the request
            $this->route();
        }
        
        private function route() {
            // Get request method and URI
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = strtok($_SERVER['REQUEST_URI'], '?');
            
            // Check if route exists
            if(isset($this->routes[$method][$uri])) {
                // Route found, execute controller method
                list($controller, $action) = $this->routes[$method][$uri];
                $controllerInstance = new $controller();
                $controllerInstance->$action();
            } else {
                // Route not found, default to movies page
                $controller = new MovieController();
                $controller->browse();
            }
        }
    }

?>