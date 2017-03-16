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
        $pass  = trim($_POST['pass']);
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          die('email');
        }
        if (empty($pass)) {
          die('pass');
        }
        $check = sqlFetch('SELECT id
                           FROM `users`
                           WHERE email="'.$email.'" 
                           AND   pass = "'.md5($pass).'"');

        if ($check && $check['id']) {
          $hash = md5($_SERVER['REMOTE_ADDR'].time());
          sqlQuery('UPDATE `users`
                    SET auth_hash = "'.$hash.'"
                    WHERE id = "'.$check['id'].'"');

          changeCookie('auth_hash', $hash, 365);
          die('ok');
        }
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
      }


      $email = sqlEscape(strip_tags($_POST['email']));
      $pass  = md5($_POST['pass']);

      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('email');
      }

      if (empty($pass) || strlen($pass) < 6) {
        die('pass');
      }

    
       $hash = md5($_SERVER['REMOTE_ADDR'].time());
        changeCookie('auth_hash', $hash, 365);
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