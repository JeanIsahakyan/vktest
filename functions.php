<?php

$SQL_INITED = false;
 
function _sqlInit()
{
  global $SQL_INITED;
  if (!$SQL_INITED) {
    $SQL_INITED = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_TABLE);
    if (mysqli_connect_errno()) {
      die('Connecting to MySQL failed: '.mysqli_connect_error());
    }
  }
  return $SQL_INITED;
}

function sqlQuery($query)
{
  global $SQL_INITED;
  $res = mysqli_query(_sqlInit(), $query);
  if (!$res) {
    die("MySQL query failed: {$query} <br> ".mysqli_error($SQL_INITED));
  }
}

function sqlFetch ($query, $multiple = false)
{
  $res = sqlQuery($query);

  if ($multiple) {
    $rows = array();
    while ($row = mysqli_fetch_assoc($res)) {
      $rows[] = $row;
    }
  }else {
    $rows = mysqli_fetch_assoc($res);
  }
  return $rows;
}

function sqlId()
{
  global $SQL_INITED;
  return $SQL_INITED ? mysqli_insert_id($SQL_INITED) : false;
}

function sqlEscape($string)
{
  global $SQL_INITED;
  return $SQL_INITED ? mysqli_real_escape_string($SQL_INITED, $string) : addslashes($string);
}
function str($str)
{
 return sqlEscape(trim(htmlspecialchars($str)));
}
function changeCookie($name, $value = '', $date = 0){
  $date =  time() + ($date * 86400);
  return setcookie($name, $value, $date, "/", '.'.$_SERVER['HTTP_HOST'], null, true);
}
?>