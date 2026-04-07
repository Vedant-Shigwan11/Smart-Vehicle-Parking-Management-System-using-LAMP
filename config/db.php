<?php
declare(strict_types=1);

$dbHost = 'localhost';
$dbName = 'smart_parking';
$dbUser = 'root';
$dbPass = '';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
