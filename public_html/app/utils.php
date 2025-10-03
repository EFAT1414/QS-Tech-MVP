<?php
function sanitize($s){ return htmlspecialchars((string)$s ?? '', ENT_QUOTES, 'UTF-8'); }
function parse_keywords($m){ $m=strtolower($m??''); $hits=[]; foreach(['quote','status','budget'] as $k){ if(strpos($m,$k)!==false)$hits[]=$k;} return implode(',',$hits); }
function audit_log($type,$payload=[]){ try{ $pdo=db(); $stmt=$pdo->prepare('INSERT INTO audit_log (event_type,payload_json,created_at) VALUES (?,?,NOW())'); $stmt->execute([$type,json_encode($payload,JSON_UNESCAPED_UNICODE)]);}catch(Throwable $e){ error_log('audit failed: '.$e->getMessage()); } }
function h1($t){ echo '<h1>'.sanitize($t).'</h1>'; }
