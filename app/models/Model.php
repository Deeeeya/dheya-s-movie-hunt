<?php

    namespace app\models;

    abstract class Model {
        
        protected $table;
        
        public function findAll() {
            $query = "SELECT * FROM {$this->table}";
            return $this->query($query);
        }
        
        public function findById($id) {
            $query = "SELECT * FROM {$this->table} WHERE id = ?";
            $result = $this->query($query, [$id]);
            return $result ? $result[0] : false;
        }
        
        public function findBy($column, $value) {
            $query = "SELECT * FROM {$this->table} WHERE {$column} = ?";
            return $this->query($query, [$value]);
        }
        
        protected function query($query, $params = []) {
            try {
                $db = $this->connect();
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Log error for debugging
                error_log("Database query error: " . $e->getMessage());
                return false;
            }
        }
        
        protected function execute($query, $params = []) {
            try {
                $db = $this->connect();
                $stmt = $db->prepare($query);
                return $stmt->execute($params);
            } catch (\PDOException $e) {
                // Log error for debugging
                error_log("Database execute error: " . $e->getMessage());
                return false;
            }
        }
        
        protected function connect() {
            // Get database credentials
            $db_host = DBHOST;
            $db_name = DBNAME;
            $db_user = DBUSER;
            $db_pass = DBPASS;
            
            // Debug connection info (comment out in production)
            error_log("Connecting to database: {$db_name} on {$db_host} as {$db_user}");
            
            try {
                // Create PDO connection with error handling
                $options = [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
                $db = new \PDO($dsn, $db_user, $db_pass, $options);
                
                return $db;
            } catch (\PDOException $e) {
                // Log the error with connection details (without password)
                error_log("Database connection failed - Host: {$db_host}, User: {$db_user}, Database: {$db_name}");
                error_log("PDO Error: " . $e->getMessage());
                
                // Rethrow with a cleaner message
                throw new \PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode());
            }
        }
    }

?>