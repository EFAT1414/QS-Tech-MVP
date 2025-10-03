<?php
require_once __DIR__.'/config.php';
function send_mail($to,$subject,$html){
  $headers='MIME-Version: 1.0'."\r\n";
  $headers.='Content-type: text/html; charset=utf-8'."\r\n";
  $headers.='From: '.MAIL_FROM_NAME.' <'.MAIL_FROM.'>'."\r\n";
  return mail($to,$subject,$html,$headers);
}
