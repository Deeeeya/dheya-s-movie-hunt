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
                // Log error or handle it as needed
                return false;
            }
        }
        
        protected function execute($query, $params = []) {
            try {
                $db = $this->connect();
                $stmt = $db->prepare($query);
                return $stmt->execute($params);
            } catch (\PDOException $e) {
                // Log error or handle it as needed
                return false;
            }
        }
        
        protected function connect() {
            $db_host = DBHOST;
            $db_name = DBNAME;
            $db_user = DBUSER;
            $db_pass = DBPASS;
            
            $db = new \PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $db;
        }
    }

?>