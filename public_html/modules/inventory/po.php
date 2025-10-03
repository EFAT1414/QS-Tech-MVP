<?php
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/utils.php';
$sku=$_GET['sku']??''; $pdo=db();
$stmt=$pdo->prepare('SELECT * FROM inventory WHERE sku=?'); $stmt->execute([$sku]); $item=$stmt->fetch();
if(!$item){ http_response_code(404); echo 'Item not found'; exit; }
$reorder=max($item['threshold']*2, $item['threshold']-$item['qty']+10);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Draft PO</title>
<style>body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:40px}.box{border:1px solid #ddd;border-radius:8px;padding:20px;max-width:720px}</style></head>
<body><div class="box">
<h1>Draft Purchase Order</h1>
<p><strong>Date:</strong> <?= date('Y-m-d') ?></p>
<p><strong>Supplier:</strong> <?= sanitize($item['supplier'] ?: 'TBD') ?></p>
<p><strong>SKU:</strong> <?= sanitize($item['sku']) ?> — <strong><?= sanitize($item['name']) ?></strong></p>
<p><strong>Current Qty:</strong> <?= (int)$item['qty'] ?> / <strong>Threshold:</strong> <?= (int)$item['threshold'] ?></p>
<p><strong>Suggested Reorder Qty:</strong> <?= (int)$reorder ?></p>
<hr><p>This is a draft PO. Adjust quantities as needed before sending.</p>
<button onclick="window.print()">Print</button>
</div></body></html>
