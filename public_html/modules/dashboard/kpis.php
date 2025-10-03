<?php
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/jira.php';
require_once __DIR__.'/../../app/utils.php';
$pdo=db();
$low=(int)$pdo->query('SELECT COUNT(*) c FROM inventory WHERE qty < threshold')->fetch()['c'] ?? 0;
$avg=(float)($pdo->query('SELECT AVG(NULLIF(budget, "")) a FROM leads')->fetch()['a'] ?? 0);
$overdue=0;
$open=0; $done7=0;
$s=jira_search_jql('project='.JIRA_PROJECT.' AND statusCategory in ("To Do","In Progress")'); if($s && isset($s['total'])) $open=(int)$s['total'];
$s2=jira_search_jql('project='.JIRA_PROJECT.' AND statusCategory = Done AND updated >= -7d'); if($s2 && isset($s2['total'])) $done7=(int)$s2['total'];
function tile($l,$v){ echo '<div class="tile"><div class="val">'.sanitize($v).'</div><div class="lab">'.sanitize($l).'</div></div>'; }
?>
<!doctype html><html><head><meta charset="utf-8"><title>KPI Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:40px}.wrap{max-width:980px;margin:auto}.tiles{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}.tile{border:1px solid #ddd;border-radius:12px;padding:16px;background:#fafafa}.val{font-size:28px;font-weight:700}.lab{color:#555}a{color:#0c63e4;text-decoration:none}</style></head>
<body><div class="wrap">
<h1>KPI Dashboard</h1><p><a href="/index.php">Home</a></p>
<div class="tiles">
<?php tile('Open Jira issues', $open or 'N/A'); ?>
<?php tile('Completed this week', $done7 or 'N/A'); ?>
<?php tile('Overdue (local)', $overdue); ?>
<?php tile('Low-stock count', $low); ?>
<?php tile('Average lead budget', $avg ? number_format($avg,2) : '—'); ?>
</div></div></body></html>
