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
  $res = mysqli_query(_sqlInit(), $query);
  if (!$res) {
    die("MySQL query failed: {$query} <br> ".mysqli_error());
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
    $rows = mysql_fetch_assoc($res);
  }
  return $rows;
}

function sqlId()
{
  global $SQL_INITED;
  return $SQL_INITED ? mysql_insert_id($SQL_INITED) : false;
}

function sqlEscape($string)
{
  global $SQL_INITED;
  return $SQL_INITED ? mysqli_real_escape_string($SQL_INITED, $string) : addslashes($string);
}

?>