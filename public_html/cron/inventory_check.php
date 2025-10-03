<?php
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/mailer.php';
require_once __DIR__.'/../app/utils.php';
$pdo=db();
$rows=$pdo->query('SELECT * FROM inventory WHERE qty < threshold')->fetchAll();
$count=0;
foreach($rows as $r){
  $stmt=$pdo->prepare('SELECT id FROM alerts WHERE sku=? AND created_at >= (NOW() - INTERVAL 1 HOUR)');
  $stmt->execute([$r['sku']]);
  if(!$stmt->fetch()){
    $pdo->prepare('INSERT INTO alerts (sku,message,status,created_at) VALUES (?,?,"open",NOW())')->execute([$r['sku'],'Low stock: '.$r['name'].' ('.$r['qty'].'/'.$r['threshold'].')']);
    audit_log('alert.created',['sku'=>$r['sku'],'qty'=>$r['qty'],'threshold'=>$r['threshold']]);
    $body='<p>Low stock alert</p><p>SKU: '.sanitize($r['sku']).' ('.sanitize($r['name']).')</p><p>Qty/Threshold: '.$r['qty'].'/'.$r['threshold'].'</p>';
    send_mail(PM_ALERT_EMAIL,'Low stock: '.$r['name'],$body);
    $count++;
  }
}
echo 'New alerts: '.$count.PHP_EOL;
