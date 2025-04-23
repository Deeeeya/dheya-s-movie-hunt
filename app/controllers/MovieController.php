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
            if (empty($_GET['movie_id'])) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Movie ID is required'
                ]);
                return;
            }
            
            $movieId = filter_var($_GET['movie_id'], FILTER_SANITIZE_NUMBER_INT);
            
            $reviewModel = new Review();
            $reviews = $reviewModel->getReviewsByMovieId($movieId);
            
            $avgRating = $reviewModel->getAverageRating($movieId);
            
            $this->json([
                'status' => 'success',
                'data' => $reviews,
                'avg_rating' => $avgRating
            ]);
        }
        
        public function addReview() {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            error_log("Received review data: " . print_r($data, true));
            
            if(!$data) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Invalid JSON data'
                ]);
                return;
            }
            
            try {
                $reviewModel = new Review();
                $result = $reviewModel->addReview($data);
                
                error_log("Review addition result: " . print_r($result, true));
                
                $this->json($result);
            } catch (\Exception $e) {
                error_log("Exception adding review: " . $e->getMessage());
                
                $this->json([
                    'status' => 'error',
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
            }
        }
    }

?>