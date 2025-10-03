<?php
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/utils.php';
require_once __DIR__.'/../../app/mailer.php';
require_once __DIR__.'/../../app/jira.php';

$errors=[]; $success=null; $createdKey=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??'');
  $budget=trim($_POST['budget']??''); $message=trim($_POST['message']??'');
  if($name==='') $errors[]='Name is required.';
  if($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Invalid email.';
  if(!$errors){
    $pdo=db(); $keywords=parse_keywords($message);
    $stmt=$pdo->prepare('INSERT INTO leads (name,email,phone,budget,message,keywords,created_at) VALUES (?,?,?,?,?,?,NOW())');
    $stmt->execute([$name,$email,$phone,$budget,$message,$keywords]);
    $lead_id=$pdo->lastInsertId();
    audit_log('lead.created',['lead_id'=>$lead_id,'email'=>$email,'keywords'=>$keywords]);
    if($email){ send_mail($email,'Thanks — we received your request','<p>Hi '.sanitize($name).',</p><p>Thanks, we will reply soon.</p>'); }
    $summary='Lead from '.$name;
    $desc="Name: $name\nEmail: $email\nPhone: $phone\nBudget: $budget\nKeywords: $keywords\nMessage:\n$message";
    $key=jira_create_issue($summary,$desc);
    if($key){
      $pdo->prepare('INSERT INTO projects (lead_id,jira_key,created_at) VALUES (?,?,NOW())')->execute([$lead_id,$key]);
      audit_log('project.created',['lead_id'=>$lead_id,'jira_key'=>$key]);
      $createdKey=$key; $success='Lead saved. Jira issue created: '.$key;
    } else { $success='Lead saved. (Jira failed — see logs)'; }
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Lead Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:40px}input,textarea{width:100%;padding:8px;margin:6px 0;border:1px solid #ccc;border-radius:6px}.btn{padding:10px 14px;border:0;background:#111;color:#fff;border-radius:8px;cursor:pointer}.card{max-width:720px;margin:auto}.error{background:#ffe5e5;padding:10px;border-radius:8px;margin-bottom:10px}.ok{background:#e6ffed;padding:10px;border-radius:8px;margin-bottom:10px}a{color:#0c63e4;text-decoration:none}</style>
</head><body><div class="card">
<h1>Lead Form</h1><p><a href="/index.php">Home</a></p>
<?php if($errors): ?><div class="error"><strong>Please fix:</strong><ul><?php foreach($errors as $e) echo '<li>'.sanitize($e).'</li>'; ?></ul></div><?php endif; ?>
<?php if($success): ?><div class="ok"><?= sanitize($success) ?></div><?php if($createdKey): ?><p>Open in Jira: <a target="_blank" href="<?= sanitize(JIRA_BASE) ?>/browse/<?= sanitize($createdKey) ?>"><?= sanitize($createdKey) ?></a></p><?php endif; ?><?php endif; ?>
<form method="post">
<label>Name</label><input name="name" required>
<label>Email</label><input name="email" type="email" placeholder="optional">
<label>Phone</label><input name="phone" placeholder="optional">
<label>Budget</label><input name="budget" placeholder="$">
<label>Message</label><textarea name="message" rows="5"></textarea>
<button class="btn" type="submit">Submit</button>
</form></div></body></html>
