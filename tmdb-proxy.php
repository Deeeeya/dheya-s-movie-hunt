<?php

    // A simplified proxy for TMDB API that works with JWT tokens
    // Place this file in your /public directory

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Load environment variables from .env file
    $env_file = __DIR__ . '/../.env';
    if (file_exists($env_file)) {
        $env = parse_ini_file($env_file);
        $jwt_token = $env['TMDB_API_KEY'] ?? '';
    } else {
        die('Error: .env file not found');
    }

    if (empty($jwt_token)) {
        die('Error: TMDB_API_KEY not found in .env file');
    }

    // Get request path
    $path = $_SERVER['PATH_INFO'] ?? '';
    if (empty($path)) {
        $path = '/movie/popular';
    }

    // Build URL
    $base_url = 'https://api.themoviedb.org/3';
    $url = $base_url . $path;

    // Add query parameters
    $first = true;
    foreach ($_GET as $key => $value) {
        $url .= ($first ? '?' : '&') . $key . '=' . urlencode($value);
        $first = false;
    }

    // If no query parameters were added, add language
    if ($first) {
        $url .= '?language=en-US';
    }

    // Make request to TMDB API
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

    // Check for errors
    if (curl_errno($ch)) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'API request failed: ' . curl_error($ch)
        ]);
        exit;
    }

    curl_close($ch);

    // Return response
    header('Content-Type: application/json');
    echo $response;

?>