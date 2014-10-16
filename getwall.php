<?php
      $dbserver = 'localhost';
      $dbuser = 'e0v3rm1n_root';
      $dbpass =  '';
      $dbname = 'e0v3rm1n_androidproject';
      $limit = 100;
      
           
      mysql_connect($dbserver, $dbuser, $dbpass) or die(mysql_error());
      mysql_select_db($dbname);
      
      $result = mysql_query("select users.name,message.message from users,message where message.uid=users.unique_id  order by ts desc limit $limit");
      if ($result && mysql_num_rows($result))
      {
	$numrows = mysql_num_rows($result);
	$rowcount = 1;
	while ($row = mysql_fetch_assoc($result))
	{
	  while(list($var,$val) = each($row))
	  {    
	    if ($var == "name") $name = $val;
	    if ($var == "message") $message = $val;	    
	  }
	  $response[] = array($name,$message);
	  ++$rowcount;
	}
      }   
      
     mysql_close();      

    if ($response != null)
    {
    	echo json_encode($response);
    }
    else
    {
    	echo "[]";
    }



?>
