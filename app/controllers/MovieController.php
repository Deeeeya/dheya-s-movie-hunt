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
            
            $movieId = filter_var($_GET['movie_id'], FILTER_SANITIZE_NUMBER_INT);
            
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
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Get POST data as JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            // Debug - log received data
            error_log("Received review data: " . print_r($data, true));
            
            if(!$data) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Invalid JSON data'
                ]);
                return;
            }
            
            // Use the Review model to add a review
            try {
                $reviewModel = new Review();
                $result = $reviewModel->addReview($data);
                
                // Debug - log result
                error_log("Review addition result: " . print_r($result, true));
                
                $this->json($result);
            } catch (\Exception $e) {
                // Log exception
                error_log("Exception adding review: " . $e->getMessage());
                
                $this->json([
                    'status' => 'error',
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
            }
        }
    }

?>