<?php

    namespace app\models;

    class Movie extends Model {
        
        protected $table = 'movies';
        
        /**
         * @param array $movieData
         * @return int|bool
         */
        public function saveMovie($movieData) {
            $existingMovie = $this->findBy('tmdb_id', $movieData['id']);
            
            if($existingMovie) {
                return $existingMovie[0]['id'];
            }
            
            $title = htmlspecialchars($movieData['title']);
            $overview = htmlspecialchars($movieData['overview'] ?? '');
            $releaseDate = htmlspecialchars($movieData['release_date'] ?? null);
            $posterPath = htmlspecialchars($movieData['poster_path'] ?? null);
            $tmdbId = (int)$movieData['id'];
            
            $query = "INSERT INTO {$this->table} (tmdb_id, title, overview, release_date, poster_path) 
                    VALUES (?, ?, ?, ?, ?)";
                    
            $result = $this->execute($query, [$tmdbId, $title, $overview, $releaseDate, $posterPath]);
            
            if($result) {
                $db = $this->connect();
                return $db->lastInsertId();
            }
            
            return false;
        }
        
        /**
         * @param int $tmdbId
         * @return array|bool
         */
        public function getByTmdbId($tmdbId) {
            return $this->findBy('tmdb_id', $tmdbId);
        }
        
        /**
         * @param string $title
         * @return array|bool
         */
        public function searchByTitle($title) {
            $query = "SELECT * FROM {$this->table} WHERE title LIKE ?";
            return $this->query($query, ['%' . $title . '%']);
        }
        
        /**
         * @param int $limit
         * @return array|bool
         */
        public function getRecentMovies($limit = 10) {
            $query = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT ?";
            return $this->query($query, [$limit]);
        }
    }

?>