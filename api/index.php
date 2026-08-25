<?php

header('Content-Type: application/json');

$host = getenv('DB_HOST');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_DATABASE');
$port = (int) (getenv('DB_PORT') ?: 3306);

if (!$host || !$username || !$password || !$database) {
    echo json_encode([
        'success' => false,
        'error' => 'Database environment variables are missing',
        'variables' => [
            'DB_HOST' => $host ? 'SET' : 'MISSING',
            'DB_USERNAME' => $username ? 'SET' : 'MISSING',
            'DB_PASSWORD' => $password ? 'SET' : 'MISSING',
            'DB_DATABASE' => $database ? 'SET' : 'MISSING',
            'DB_PORT' => $port
        ]
    ]);
    exit;
}

try {

    $con = mysqli_connect(
        $host,
        $username,
        $password,
        $database,
        $port
    );

    if (!$con) {
        throw new Exception(mysqli_connect_error());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Database connected successfully'
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
