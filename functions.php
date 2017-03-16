<?php

$SQL_INITED = array();
 
function _sqlInit($server = false)
{
  global $SQL_INITED, $MYSQL_SERVERS;
  if (!$server) {
    $server = 'default';
  }
  
  if (!isset($SQL_INITED[$server])) {

    $config = $MYSQL_SERVERS[$server];

    $SQL_INITED[$server]  = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_TABLE']);

    if (mysqli_connect_errno()) {
      die('Connecting to MySQL failed: '.mysqli_connect_error());
    }
  }
  return $SQL_INITED[$server];
}

function sqlQuery($query, $server = false)
{
  $connection = _sqlInit($server);

  $res = mysqli_query($connection, $query);
  
  if (!$res) {
    die("MySQL query failed: {$query} <br> ".mysqli_error($connection));
  }
  return $res;
}

function sqlFetch ($query, $multiple = false, $server = false)
{
  $res = sqlQuery($query, $server);

  if ($multiple) {
    $rows = array();
    while ($row = mysqli_fetch_assoc($res)) {
      $rows[] = $row;
    }
  }else {
    $rows = mysqli_fetch_assoc($res);
  }
  
  mysqli_free_result($res);

  return $rows;
}

function sqlId($server = false)
{
  $connection = _sqlInit($server);

  return $connection ? mysqli_insert_id($connection) : false;
}

function sqlEscape($string, $server = false)
{
  $connection = _sqlInit($server);
  return $connection ? mysqli_real_escape_string($connection, $string) : addslashes($string);
}

function sqlCloseConnection($server = false)
{
  $connection = _sqlInit($server);
  return $connection ? mysqli_close($connection) : false;
}

function str($str)
{
 return sqlEscape(trim(htmlspecialchars($str)));
}
function changeCookie($name, $value = '', $date = 0)
{
  $date =  time() + ($date * 86400);
  return setcookie($name, $value, $date, "/", '.'.$_SERVER['HTTP_HOST'], null, true);
}

function genHash($str = '')
{
  global $owner;
  return md5($str.$_SERVER['REMOTE_ADDR'].$owner['id']);
}
?>