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
        }else {
          die('unknown');
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
      } elseif ($type == 2 && !$balance) {
        die('balance');
      }


      $email = sqlEscape(strip_tags($_POST['email']));
      $pass  = trim($_POST['pass']);

      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('email');
      }

      $check = sqlFetch('SELECT 
                        COUNT(id) as cnt
                        FROM `users`
                        WHERE email = "'.$email.'"');
      
      if ($check['cnt']) {
        die('used_email');
      }

      if (empty($pass) || strlen($pass) < 6) {
        die('pass');
      }


      $hash = md5($_SERVER['REMOTE_ADDR'].time());
  
      sqlQuery('INSERT INTO `users`
                SET first_name = "'.$first_name.'",
                    last_name = "'.$last_name.'",
                    type = "'.$type.'",
                    balance = "'.$balance.'",
                    email = "'.$email.'",
                    pass = "'.md5($pass).'",
                    auth_hash = "'.$hash.'"');

      if (sqlId()) {
           changeCookie('auth_hash', $hash, 365);;
           die ('ok');
      } else {
        die ('unknown');
      }
      break;

    default:
      $title = 'Вход';
      include_once('tpl/not_logged.php');
      break;
  }
} else {
  switch ($act) {
    case 'logout':
       changeCookie('auth_hash', '', 0);
       exit;
      break;
    case 'add_task':
      break;
    case 'submit_task':
      break;
    default:
        $title = 'Список продуктов';
        $offset = (int) $_POST['offset'];
        $limit = 10;
        if ($owner['type'] == 2){
           $where = 'owner_id = "'.$owner['id'].'"';
        } else {
          $where = 'user_id = "0"';
        }

        $products = sqlFetch('SELECT title,
                                      descr,
                                      price 
                              FROM `products`
                              WHERE '.$where.'
                              ORDER BY `date` DESC
                              LIMIT '.$limit.' 
                              OFFSET '.$offset, true);

        if ($offset) {
          $res = array('content' => ob_get_clean(), 'offset' => $new_offset);
          echo json_encode($res);
          exit;
        }
      break;
  }
}



if (!$title) {
  $title = 'vktest';
}

$content = ob_get_clean();

include_once('tpl/main.php');
?>