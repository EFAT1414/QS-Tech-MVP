<?php
require_once __DIR__.'/app/db.php';
try {
  $pdo = db();
  echo "OK: connected to DB ".htmlspecialchars(DB_NAME);
  $r = $pdo->query("SHOW TABLES")->fetchAll();
  echo "<pre>".print_r($r, true)."</pre>";
} catch (Throwable $e) {
  echo $e;
}
