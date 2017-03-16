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


if (!$title) {
  $title = 'vktest';
}

include('tpl/main.php');