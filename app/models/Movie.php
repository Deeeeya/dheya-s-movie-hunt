<?php

    namespace app\models;

    class Movie extends Model {
        
        protected $table = 'movies';
        
        /**
         * Save movie details to the database if not already saved
         * This allows us to keep a local cache of movies for faster access
         * 
         * @param array $movieData
         * @return int|bool
         */
        public function saveMovie($movieData) {
            // Check if movie already exists
            $existingMovie = $this->findBy('tmdb_id', $movieData['id']);
            
            if($existingMovie) {
                return $existingMovie[0]['id'];
            }
            
            // Movie doesn't exist, insert it
            $title = htmlspecialchars($movieData['title']);
            $overview = htmlspecialchars($movieData['overview'] ?? '');
            $releaseDate = htmlspecialchars($movieData['release_date'] ?? null);
            $posterPath = htmlspecialchars($movieData['poster_path'] ?? null);
            $tmdbId = (int)$movieData['id'];
            
            $query = "INSERT INTO {$this->table} (tmdb_id, title, overview, release_date, poster_path) 
                    VALUES (?, ?, ?, ?, ?)";
                    
            $result = $this->execute($query, [$tmdbId, $title, $overview, $releaseDate, $posterPath]);
            
            if($result) {
                // Get the inserted ID
                $db = $this->connect();
                return $db->lastInsertId();
            }
            
            return false;
        }
        
        /**
         * Get movie by TMDB ID
         * 
         * @param int $tmdbId
         * @return array|bool
         */
        public function getByTmdbId($tmdbId) {
            return $this->findBy('tmdb_id', $tmdbId);
        }
        
        /**
         * Search for movies by title
         * 
         * @param string $title
         * @return array|bool
         */
        public function searchByTitle($title) {
            $query = "SELECT * FROM {$this->table} WHERE title LIKE ?";
            return $this->query($query, ['%' . $title . '%']);
        }
        
        /**
         * Get recently added or updated movies
         * 
         * @param int $limit
         * @return array|bool
         */
        public function getRecentMovies($limit = 10) {
            $query = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT ?";
            return $this->query($query, [$limit]);
        }
    }

?>