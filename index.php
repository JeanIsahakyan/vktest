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
  if (!$owner) {
    changeCookie('auth_hash', '', 0);
    header('Location: /');
  }
  unset($auth_hash);
}else {
  $owner = false;
}

$act = $_GET['act'];

ob_start();

if (!$owner) {
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
    case 'add_product_box':
        if ($owner['type'] != 2) {
          die('error');
        }
        include('tpl/add_product_box.php');
        die(ob_get_clean());
      break;
    case 'logout':
       if ($_POST['hash'] != genHash('logOut')) {
        die('error'); 
       } 
       changeCookie('auth_hash', '', 0);
       exit;
      break;
    case 'add_product':
      if ($_POST['hash'] != genHash('addProduct') || $owner['type'] != 2) {
        die('error');
      }

      $mem = new Memcached;
      $mem->addServer($MEM_HOST, $MEM_PORT);

      $title = str($_POST['title']);
      $descr = str($_POST['descr']);
      $price = (float) $_POST['price'];

      if (empty($title) || strlen($title) < 4) {
        die('title');
      } elseif (empty($descr) || strlen($descr) < 40) {
        die('descr');
      } elseif (!$price) {
        die('price');
      } elseif ($price > $owner['balance']) {
        die('low_price');
      }

      if (!$mem->add('product_flood'.$owner['id'], 1, 60)) { 
        die('flood');
      }

      $balance = ($owner['balance'] - $price);

      sqlQuery('INSERT INTO `products`
                SET title = "'.$title.'",
                   descr = "'.$descr.'",
                   price = "'.$price.'",
                   owner_id = "'.$owner['id'].'"
                   ', 'server1'); // other server

     sqlQuery('UPDATE `users`
              SET balance = "'.$balance.'"
              WHERE id = "'.$owner['id'].'"');

       die('ok|'.$balance);

      break;
    case 'statistics':
      $title = 'Статистика';
      $limit = 10;
      $offset = (int)$_POST['offset'];
      $statistics = sqlFetch('SELECT product_id,
                                     user_id,
                                     commission
                                     FROM `system_commission`
                                     ORDER BY `commission`
                                     DESC LIMIT '.$limit.'
                                    OFFSET '.$offset, true); 
      
      $new_offset = count($statistics) + $offset;

      $products = $users = array();

      if ($statistics) {
        $uids = $products_ids = array();
        foreach($statistics as $stat) {
            $uids[$stat['user_id']] = true;
            $products_ids[] = $stat['product_id'];
        }

        $products_list = sqlFetch('SELECT title,
                                          id,
                                          owner_id,
                                          price
                                    FROM `products` 
                                    WHERE id IN('.join(',', $products_ids).')', true, 'server1'); //other server
        if ($products_list) {
          foreach ($products_list as $product) {
            $products[$product['id']] = $product;
            $uids[$product['owner_id']] = true;
          }
        }

        $users_list = sqlFetch('SELECT first_name,
                                       last_name,
                                       id
                               FROM `users`
                               WHERE id IN('.join(',', array_keys($uids)).')', true);
        if ($users_list) {
          foreach ($users_list as $user) {
            $users[$user['id']] = $user;
          }
        }

        unset($stat, $products_list, $products_ids, $uids, $users_list, $user);
      }

      include('tpl/stats_item.php');

      if ($offset) {
        $res = array('content' => $stats_list, 'offset' => $new_offset);
        echo json_encode($res);
        exit;
      }

      include('tpl/stats_main.php');


      break;

    case 'submit_product':
      $id = (int) $_POST['id'];
      
      if ($_POST['hash'] != genHash('product'.$id) || $owner['type'] != 1) {
        die ('error');
      }


      $mem = new Memcached;
      $mem->addServer($MEM_HOST, $MEM_PORT);

      $time = $_SERVER['REQUEST_TIME'];

      $key = 'submit_flood'.$time.$owner['id'];

      if (!$mem->set($key, 1, 1)) {
        $mem->increment($key);
      }

      if ($mem->get($key) > 2) {
        die('flood');
      }

      $product = sqlFetch('SELECT price 
                           FROM `products`
                           WHERE id="'.$id.'"
                           AND user_id = "0"', false, 'server1'); //other server
      if (!$product) {
         die('error');
      }

      $system = $product['price'] * ($SYSTEM_COMMISSION / 100);
      $user = round($product['price'] - $system, 2);

      $changed = sqlQuery('UPDATE `products`
                SET user_id ="'.$owner['id'].'"
                WHERE id = "'.$id.'"
                AND user_id = "0"', 'server1'); //other server

      if (!$changed) {
        die('error');
      }

      sqlQuery('INSERT INTO `system_commission`
                SET commission = "'.$system.'",
                    product_id = "'.$id.'",
                    user_id = "'.$owner['id'].'" ');

      $balance = round($owner['balance'] + $user);

      sqlQuery('UPDATE `users`
                SET balance = "'.$balance.'"
                WHERE id = "'.$owner['id'].'"');

      $res = array('balance' => $balance, 'price' => $user);
      echo json_encode($res);
      exit;

      break;
    default:
        $title = 'Список продуктов';
        $offset = (int) $_POST['offset'];
        $limit = 10;
        if ($owner['type'] == 2) {
           $where = 'owner_id = "'.$owner['id'].'"'.($_GET['finished'] ? ' AND user_id != "0"' : '');
        } else {
          $where = 'user_id = "'.($_GET['finished'] ? $owner['id'] : 0).'"';
        }

        $products = sqlFetch('SELECT id,
                                      title,
                                      descr,
                                      price,
                                      user_id,
                                      owner_id
                              FROM `products`
                              WHERE '.$where.'
                              ORDER BY `date` DESC
                              LIMIT '.$limit.' 
                              OFFSET '.$offset, true, 'server1'); // fetching from other server

        if ($owner['type'] == 2 && $products) {
          $uids = $users = array();

          foreach($products as $uid) {
            if ($uid['user_id']) {
              $uids[] = $uid['user_id'];
            } 
          }

          if ($uids) {

            $users_data = sqlFetch('SELECT first_name,
                                         last_name,
                                         id
                                  FROM  `users`
                                  WHERE id IN('.join(',', $uids).')', true);

            foreach($users_data as $uid) {
              $user[$uid['id']] = $uid;
            }
            unset($users_data, $uid, $uids);
          }
        }

        $new_offset = $offset + count($products);
        
        include ('tpl/products_list.php');

        if ($offset) {
          $res = array('content' => ($products ? $products_list : ''), 'offset' => $new_offset);
          echo json_encode($res);
          exit;
        }
        include('tpl/products.php');

      break;
  }
}



if (!$title) {
  $title = 'vktest';
}

$content = ob_get_clean();

if (isset($_POST['ajax'])) {
  echo json_encode(array('content' => $content, 'title' => $title));
}else {
   include_once('tpl/main.php');
}

?>