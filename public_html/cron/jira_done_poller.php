<?php
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/utils.php';
require_once __DIR__.'/../app/mailer.php';
require_once __DIR__.'/../app/jira.php';

$res = jira_search_jql('project='.JIRA_PROJECT.' AND statusCategory = Done AND updated >= -60m');
if (!$res || !isset($res['issues'])) {
  echo 'Jira search failed or no issues found'.PHP_EOL;
  exit;
}

$pdo  = db();
$sent = 0;

foreach (($res['issues'] ?? []) as $iss) {
  $key = $iss['key'] ?? null;
  if (!$key) continue;

  // Map Jira issue back to lead
  $stmt = $pdo->prepare('
    SELECT leads.email, leads.name
    FROM projects
    JOIN leads ON leads.id = projects.lead_id
    WHERE projects.jira_key = ?
  ');
  $stmt->execute([$key]);
  $row = $stmt->fetch();
  if (!$row || !$row['email']) continue;

  // Idempotency: avoid emailing the same key twice within 2 hours
  $chk = $pdo->prepare('
    SELECT id
    FROM audit_log
    WHERE event_type = "email.completion"
      AND payload_json LIKE ?
      AND created_at >= (NOW() - INTERVAL 2 HOUR)
  ');
  
  $chk->execute(['%' . $key . '%']);
  if ($chk->fetch()) continue;

  // Send email
  $ok = send_mail(
    $row['email'],
    'Your project is complete',
    '<p>Hi '.sanitize($row['name']).',</p><p>Your job is complete. We value your feedback. We also offer maintenance plans.</p>'
  );

  if ($ok) {
    audit_log('email.completion', ['jira_key' => $key, 'to' => $row['email']]);
    $sent++;
  }
}

echo 'Completion emails sent: '.$sent.PHP_EOL;
