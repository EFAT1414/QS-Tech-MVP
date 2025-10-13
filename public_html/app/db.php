<?php
require_once __DIR__.'/config.php';
// Local override (NOT committed)
if (file_exists(__DIR__.'/config.local.php')) {
  require_once __DIR__.'/config.local.php';
}

function db() {
  static $pdo;
  if ($pdo) return $pdo;
  $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  } catch (PDOException $e) {
    die('<pre><strong>Database connection failed.</strong>'
      ."\nHost: ".htmlspecialchars(DB_HOST)
      ."\nDB: ".htmlspecialchars(DB_NAME)
      ."\nUser: ".htmlspecialchars(DB_USER)
      ."\n\nError: ".$e->getMessage()
      ."</pre>");
  }
  return $pdo;
}
