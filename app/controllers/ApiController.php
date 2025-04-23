<?php

    namespace app\controllers;

    use app\models\Movie;

    class ApiController extends Controller {
        
        public function getMovies() {
            $query = isset($_GET['query']) ? $_GET['query'] : null;
            
            if ($query) {
                $url = "https://api.themoviedb.org/3/search/movie?language=en-US&query=" . urlencode($query) . "&page=1";
            } else {
                $url = "https://api.themoviedb.org/3/movie/popular?language=en-US&page=1";
            }
            
            $response = $this->makeApiRequest($url);
            $this->json($response);
        }
        
        public function getMovieDetails() {
            if (empty($_GET['id'])) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Movie ID is required'
                ]);
                return;
            }
            
            $movieId = $_GET['id'];
            
            $url = "https://api.themoviedb.org/3/movie/" . $movieId . "?language=en-US";
            $response = $this->makeApiRequest($url);
            $this->json($response);
        }
        
        private function makeApiRequest($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $headers = [
                'Authorization: Bearer ' . TMDB_API_KEY,
                'Content-Type: application/json;charset=utf-8'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                return [
                    'status' => 'error',
                    'message' => 'API request failed: ' . curl_error($ch),
                    'curl_error_code' => curl_errno($ch)
                ];
            }
            
            curl_close($ch);
            
            $parsed = json_decode($response, true);
            
            if ($parsed === null) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to parse API response: ' . json_last_error_msg(),
                    'raw_response' => substr($response, 0, 200) 
                ];
            }
            
            return $parsed;
        }
    }

?>