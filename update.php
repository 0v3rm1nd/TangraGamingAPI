<?php


if ((isset($_POST['id']) == true) && ($_POST['id'] != '') &&
    (isset($_POST['message']) == true) && ($_POST['message'] != '')) 
    {    
      $dbserver = 'localhost';
      $dbuser = 'root';
      $dbpass =  '0v3rm1nd';
      $dbname = 'androidproject';
      
      $id = $_POST['id'];
      $message = $_POST['message'];
      
      echo $message;
      echo $id;
      
      mysql_connect($dbserver, $dbuser, $dbpass) or die(mysql_error());
      mysql_select_db($dbname);
      
      $result = mysql_query("INSERT INTO message (uid,message) VALUES (\"$id\",\"$message\")");

      mysql_close();       
      
      
    }

?>
