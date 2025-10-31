<?php
// Jeg forbinder til databasen via MAMP (localhost)
// — MAMP bruger port 8889 og har som standardbrugernavn/løs "root"

$host = "127.0.0.1";
$port = 8889;
$socket = "/Applications/MAMP/tmp/mysql/mysql.sock";
$db   = "tarot";       // mit database-navn
$user = "root";
$password = "root";

// Jeg laver to mulige forbindelser (dsn’er):
// 1) via unix_socket (hurtigst på Mac)
// 2) via host + port (fallback, hvis socket ikke virker)
$dsnCandidates = [
  "mysql:unix_socket=$socket;dbname=$db;charset=utf8mb4",
  "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
];

$pdo = null;
$lastException = null;

// Jeg prøver dem én ad gangen, så scriptet ikke fejler hvis socketen er lukket
foreach ($dsnCandidates as $dsn) {
  try {
    $pdo = new PDO($dsn, $user, $password, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     // fejl skal kaste exceptions
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // resultater som associative arrays
    ]);
    break; // hvis forbindelsen lykkes, stopper jeg her
  } catch (PDOException $e) {
    $lastException = $e; // gem fejl hvis det går galt
  }
}

// Hvis ingen forbindelse lykkedes, smider jeg den sidste fejl
if (!$pdo) {
  throw $lastException ?? new RuntimeException('Could not connect to database');
}

// Returnerer PDO-objektet til den fil der kalder mig (typisk index.php)
return $pdo;