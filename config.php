<?php
$host = "127.0.0.1";
$port = 8889;
$socket = "/Applications/MAMP/tmp/mysql/mysql.sock";
$db   = "tarot";
$user = "root";
$password = "root";

$dsnCandidates = [
  "mysql:unix_socket=$socket;dbname=$db;charset=utf8mb4",
  "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
];

$pdo = null;
$lastException = null;

foreach ($dsnCandidates as $dsn) {
  try {
    $pdo = new PDO($dsn, $user, $password, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    break;
  } catch (PDOException $e) {
    $lastException = $e;
  }
}

if (!$pdo) {
  throw $lastException ?? new RuntimeException('Could not connect to database');
}

return $pdo;
