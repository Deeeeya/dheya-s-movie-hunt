<?php
    // Test file for JWT token authentication with TMDB
    // Place this file in your public directory

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Load .env file directly
    $env_file = __DIR__ . '/../.env';
    if (file_exists($env_file)) {
        $env = parse_ini_file($env_file);
        $jwt_token = $env['TMDB_API_KEY'] ?? 'Not found';
        
        echo "JWT Token from .env file is " . (empty($jwt_token) ? "not found" : "loaded") . "<br>";
        
        // Test the JWT token directly
        $url = "https://api.themoviedb.org/3/movie/popular?language=en-US&page=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        // Set Bearer token for JWT authentication
        $headers = [
            'Authorization: Bearer ' . $jwt_token,
            'Content-Type: application/json;charset=utf-8'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo "CURL Error: " . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            echo "<pre>";
            
            if (isset($result['status_code']) && $result['status_code'] == 7) {
                echo "❌ API key validation error: " . $result['status_message'] . "<br>";
                echo "Please check your TMDB JWT token.";
            } elseif (isset($result['success']) && $result['success'] === false) {
                echo "❌ API error: " . ($result['status_message'] ?? 'Unknown error') . "<br>";
                echo "HTTP Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "<br>";
                echo "Response:<br>";
                print_r($result);
            } elseif (isset($result['results'])) {
                echo "✅ JWT Token is valid! Successfully retrieved " . count($result['results']) . " movies<br>";
                echo "First movie: " . $result['results'][0]['title'];
            } else {
                echo "Unexpected response:<br>";
                print_r($result);
            }
            
            echo "</pre>";
        }
        curl_close($ch);
    } else {
        echo "❌ .env file not found at: " . $env_file;
    }

?>