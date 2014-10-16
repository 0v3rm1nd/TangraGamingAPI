<?php 
function dbConnect($usertype, $connectionType = 'mysqli') { 
  $host = 'localhost'; 
  $db = 'tangra'; 
  if ($usertype  == 'read') { 
    $user = 'root'; 
    $pwd = ''; 
  } elseif ($usertype == 'write') { 
    $user = 'root'; 
    $pwd = ''; 
  } else { 
    exit('Unrecognized connection type'); 
  } 
  if ($connectionType == 'mysqli') { 
    $conn =  new mysqli($host, $user, $pwd, $db) or die ('Cannot open database');
    return $conn; 
  } else { 
    try { 
      $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pwd); 
      return $conn;
    } catch (PDOException $e) { 
      echo 'Cannot connect to database'; 
      exit; 
    } 
  } 
}
