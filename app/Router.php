<?php

    namespace app;

    use app\controllers\MainController;
    use app\controllers\MovieController;
    use app\controllers\ApiController;

    class Router {
        
        private $routes = [];
        
        public function __construct() {
            $this->routes = [ // routes are defined here
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
            
            $this->route(); // routes the request
        }
        
        private function route() {
            $method = $_SERVER['REQUEST_METHOD']; // Get request method and URI
            $uri = strtok($_SERVER['REQUEST_URI'], '?');
            
            // checks if the route exists
            if(isset($this->routes[$method][$uri])) {
                // if the route is found, controller method is executed
                list($controller, $action) = $this->routes[$method][$uri];
                $controllerInstance = new $controller();
                $controllerInstance->$action();
            } else {
                // if the route is not found, it defaults to movies page
                $controller = new MovieController();
                $controller->browse();
            }
        }
    }

?>