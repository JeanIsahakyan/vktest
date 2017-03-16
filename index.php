<?php
require_once('config.php');
require_once('functions.php');


if (isset($_COOKIE['auth_hash'])) {
  $auth_hash = sqlEscape($_COOKIE['auth_hash']);
  $owner = sqlFetch('SELECT id,
                             first_name,
                             last_name,
                             type,
                             balance 
                         FROM `users`
                         WHERE auth_hash = "'.$auth_hash.'"');
  unset($auth_hash);
}else {
  $owner = false;
}

$act = $_GET['act'];

ob_start();

if(!$owner) {
  switch ($act) {
    case 'login':
        $email = sqlEscape(strip_tags($_POST['email']));
        $pass  = md5($_POST['pass']);

      break;

    case 'register':
      $first_name = ucfirst(str($_POST['first_name']));
      $last_name  = ucfirst(str($_POST['last_name']));
      
      if (empty($first_name) || strlen($first_name) < 2) {
        die('first_name');
      } elseif (empty($last_name) || strlen($last_name) < 2) {
        die('last_name');
      }

      $type    = (int) $_POST['type'];
      $balance = (float) $_POST['balance'];

      if (!$type) { // 0
        die('type');
      } elseif (!$balance) {

      }


      $email = sqlEscape(strip_tags($_POST['email']));
      $pass  = md5($_POST['pass']);

      break;

   case 'login':
      
      break;
    
    default:
      $title = 'Вход';
      include_once('tpl/not_logged.php');
      break;
  }
} else {
  switch ($act) {
    case 'add_task':
      break;
    case 'submit_task':
      break;
    default:
   
      break;
  }
}



if (!$title) {
  $title = 'vktest';
}

$content = ob_get_clean();

include_once('tpl/main.php');
?>