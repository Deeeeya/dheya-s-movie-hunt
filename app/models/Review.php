<?php

    namespace app\models;

    class Review extends Model {
        
        protected $table = 'reviews';
        
        /**
         * @param int $movieId
         * @return array|bool
         */
        public function getReviewsByMovieId($movieId) {
            $query = "SELECT * FROM {$this->table} WHERE movie_id = ? ORDER BY created_at DESC";
            return $this->query($query, [$movieId]);
        }
        
        /**
         * @param array $data
         * @return array 
         */
        public function addReview($data) {
            if(empty($data['movie_id']) || empty($data['username']) || !isset($data['rating']) || empty($data['comment'])) {
                return [
                    'status' => 'error',
                    'message' => 'All fields are required'
                ];
            }
            
            if($data['rating'] < 1 || $data['rating'] > 5) {
                return [
                    'status' => 'error',
                    'message' => 'Rating must be between 1 and 5'
                ];
            }
            
            try {
                $movieId = filter_var($data['movie_id'], FILTER_SANITIZE_NUMBER_INT);
                $username = htmlspecialchars($data['username']);
                $rating = (int)$data['rating'];
                $comment = htmlspecialchars($data['comment']);
                $createdAt = date('Y-m-d H:i:s');
                
                $db = $this->connect();
                $query = "INSERT INTO {$this->table} (movie_id, username, rating, comment, created_at) 
                        VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($query);
                $result = $stmt->execute([$movieId, $username, $rating, $comment, $createdAt]);
                
                if($result) {
                    return [
                        'status' => 'success',
                        'message' => 'Review added successfully'
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Database error: Failed to insert review'
                    ];
                }
            } catch (\PDOException $e) {
                return [
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
        }
        
        /**
         * @param int $movieId
         * @return float|null
         */
        public function getAverageRating($movieId) {
            $query = "SELECT AVG(rating) as avg_rating FROM {$this->table} WHERE movie_id = ?";
            $result = $this->query($query, [$movieId]);
            
            if($result && isset($result[0]['avg_rating'])) {
                return round($result[0]['avg_rating'], 1);
            }
            
            return null;
        }
        
        /**
         * @param int $movieId
         * @return int
         */
        public function countReviews($movieId) {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE movie_id = ?";
            $result = $this->query($query, [$movieId]);
            
            if($result && isset($result[0]['count'])) {
                return (int)$result[0]['count'];
            }
            
            return 0;
        }
    }

?>