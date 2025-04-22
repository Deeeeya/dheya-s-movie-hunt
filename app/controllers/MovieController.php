<?php

    namespace app\controllers;

    use app\models\Review;

    class MovieController extends Controller {
        
        public function browse() {
            $this->view("movies/browse");
        }
        
        public function detail() {
            $this->view("movies/detail");
        }
        
        public function getReviews() {
            // Check if movie_id parameter is provided
            if (empty($_GET['movie_id'])) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Movie ID is required'
                ]);
                return;
            }
            
            $movieId = htmlspecialchars($_GET['movie_id']);
            
            // Use the Review model to get reviews
            $reviewModel = new Review();
            $reviews = $reviewModel->getReviewsByMovieId($movieId);
            
            // Get average rating
            $avgRating = $reviewModel->getAverageRating($movieId);
            
            $this->json([
                'status' => 'success',
                'data' => $reviews,
                'avg_rating' => $avgRating
            ]);
        }
        
        public function addReview() {
            // Get POST data as JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if(!$data) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Invalid JSON data'
                ]);
                return;
            }
            
            // Use the Review model to add a review
            $reviewModel = new Review();
            $result = $reviewModel->addReview($data);
            
            $this->json($result);
        }
    }

?>