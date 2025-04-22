<?php

    namespace app\controllers;

    use app\models\Movie;

    class ApiController extends Controller {
        
        public function getMovies() {
            // Get search query if provided
            $query = isset($_GET['query']) ? $_GET['query'] : null;
            
            if ($query) {
                // Search for movies
                $url = "https://api.themoviedb.org/3/search/movie?language=en-US&query=" . urlencode($query) . "&page=1";
            } else {
                // Get popular movies
                $url = "https://api.themoviedb.org/3/movie/popular?language=en-US&page=1";
            }
            
            // Make request to TMDB API using JWT token
            $response = $this->makeApiRequest($url);
            
            // Return response
            $this->json($response);
        }
        
        public function getMovieDetails() {
            // Check if movie_id parameter is provided
            if (empty($_GET['id'])) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Movie ID is required'
                ]);
                return;
            }
            
            $movieId = $_GET['id'];
            
            // Build URL
            $url = "https://api.themoviedb.org/3/movie/" . $movieId . "?language=en-US";
            
            // Make request to TMDB API
            $response = $this->makeApiRequest($url);
            
            // Return response
            $this->json($response);
        }
        
        private function makeApiRequest($url) {
            // Initialize curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            // Set Bearer token for JWT authentication
            $headers = [
                'Authorization: Bearer ' . TMDB_API_KEY,
                'Content-Type: application/json;charset=utf-8'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            // Execute curl request
            $response = curl_exec($ch);
            
            // Check for errors
            if (curl_errno($ch)) {
                return [
                    'status' => 'error',
                    'message' => 'API request failed: ' . curl_error($ch),
                    'curl_error_code' => curl_errno($ch)
                ];
            }
            
            // Close curl
            curl_close($ch);
            
            // Parse JSON response
            $parsed = json_decode($response, true);
            
            // Check if parsing failed
            if ($parsed === null) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to parse API response: ' . json_last_error_msg(),
                    'raw_response' => substr($response, 0, 200) // Show beginning of response for debugging
                ];
            }
            
            return $parsed;
        }
    }

?>