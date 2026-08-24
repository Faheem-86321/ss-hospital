<?php

header('Content-Type: application/json');

$con = mysqli_connect(
    getenv('srv1934.hstgr.io'),
    getenv('u719432153_Faheem'),
    getenv('DB_PASSWORD'),
    getenv('u719432153_Faheem'),
    (int) (getenv('DB_PORT') ?: 3306)
);

if (!$con) {
    echo json_encode([
        'success' => false,
        'error' => mysqli_connect_error()
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Database connected successfully'
]);
