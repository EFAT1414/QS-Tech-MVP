<?php
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/utils.php';
$pdo=db(); $msg=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $sku=trim($_POST['sku']??''); $name=trim($_POST['name']??''); $qty=(int)($_POST['qty']??0); $threshold=(int)($_POST['threshold']??0); $supplier=trim($_POST['supplier']??'');
  if($sku && $name){
    $stmt=$pdo->prepare('INSERT INTO inventory (sku,name,qty,threshold,supplier,updated_at) VALUES (?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name),qty=VALUES(qty),threshold=VALUES(threshold),supplier=VALUES(supplier),updated_at=NOW()');
    $stmt->execute([$sku,$name,$qty,$threshold,$supplier]); $msg='Saved.';
  } else { $msg='SKU and name are required.'; }
}
$rows=$pdo->query('SELECT * FROM inventory ORDER BY name')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Inventory</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:40px}input{padding:8px;margin:6px 0;border:1px solid #ccc;border-radius:6px}.btn{padding:8px 12px;border:0;background:#111;color:#fff;border-radius:8px;cursor:pointer}table{border-collapse:collapse;width:100%;margin-top:20px}th,td{border:1px solid #ddd;padding:8px}tr.low{background:#fff3cd}.wrap{max-width:980px;margin:auto}a{color:#0c63e4;text-decoration:none}</style></head>
<body><div class="wrap">
<h1>Inventory</h1><p><a href="/index.php">Home</a></p>
<?php if($msg): ?><p><strong><?= sanitize($msg) ?></strong></p><?php endif; ?>
<form method="post">
<label>SKU</label><br><input name="sku" required>
<label>Name</label><br><input name="name" required>
<label>Qty</label><br><input name="qty" type="number" value="0">
<label>Threshold</label><br><input name="threshold" type="number" value="0">
<label>Supplier</label><br><input name="supplier">
<button class="btn" type="submit">Save</button>
</form>
<table><thead><tr><th>SKU</th><th>Name</th><th>Qty</th><th>Threshold</th><th>Supplier</th><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $r): $low=($r['qty']<$r['threshold'])?'low':''; ?>
<tr class="<?= $low ?>"><td><?= sanitize($r['sku']) ?></td><td><?= sanitize($r['name']) ?></td><td><?= (int)$r['qty'] ?></td><td><?= (int)$r['threshold'] ?></td><td><?= sanitize($r['supplier']) ?></td>
<td><?php if($r['qty']<$r['threshold']): ?><a href="/modules/inventory/po.php?sku=<?= urlencode($r['sku']) ?>">Draft PO</a><?php else: ?>&mdash;<?php endif; ?></td></tr>
<?php endforeach; ?>
</tbody></table>
</div></body></html>
