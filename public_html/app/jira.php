<?php
require_once __DIR__.'/config.php';
function jira_create_issue($summary,$description){
  $url=rtrim(JIRA_BASE,'/').'/rest/api/3/issue';
  $payload=['fields'=>['project'=>['key'=>JIRA_PROJECT],'summary'=>$summary,'description'=>$description,'issuetype'=>['name'=>'Task']]];
  $ch=curl_init($url);
  curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>json_encode($payload),CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json'],CURLOPT_USERPWD=>JIRA_EMAIL.':'.JIRA_TOKEN]);
  $res=curl_exec($ch); $code=curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
  if($code>=200 && $code<300){ $data=json_decode($res,true); return $data['key']??null; }
  error_log('jira_create_issue failed HTTP '.$code); return null;
}
function jira_search_jql($jql){
  $url=rtrim(JIRA_BASE,'/').'/rest/api/3/search?jql='.urlencode($jql);
  $ch=curl_init($url);
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Accept: application/json'],CURLOPT_USERPWD=>JIRA_EMAIL.':'.JIRA_TOKEN]);
  $res=curl_exec($ch); $code=curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
  return ($code>=200 && $code<300)? json_decode($res,true): null;
}
