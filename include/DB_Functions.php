<?php

class DB_Functions {

    private $db;

    //put your code here
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $result = mysql_query("INSERT INTO users(unique_id, name, email, encrypted_password, salt, created_at) VALUES('$uuid', '$name', '$email', '$encrypted_password', '$salt', NOW())");
        // check for successful store
        if ($result) {
            // get user details 
            $uid = mysql_insert_id(); // last inserted id
            $result = mysql_query("SELECT * FROM users WHERE uid = $uid");
            // return user details
            return mysql_fetch_array($result);
        } else {
            return false;
        }
    }
    
    
function validPassword($password)
{
  $ok = true;
  if (strlen($password) < 6) $ok = false;
  
  return $ok;
}
    
    
/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.

Stole this somewhere on the Internet :)
*/
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || 
 ↪checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}    
    
    
    public function getwall($uid)
    {
      //  ===============================================================================
      //  ===============================================================================
      //  Change this when we have friends functionality working
      //  ===============================================================================
      //  ===============================================================================
                  
      $result = mysql_query("select users.name,message.message from users,message where message.uid=users.unique_id order by message.ts;");
      if ($result)
      {
	  	  
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
	      ++$rowcount;	
	      $record[] = array($name => $message);
	    }
	  }  	
	  	
	return $record;	
      }	      
      else
      {
	return false;
      }      
      
      
    }
    
    public function latestmessageid($from)
    {
      //  ===============================================================================
      //  ===============================================================================
      //  Change this when we have friends functionality working
      //  ===============================================================================
      //  ===============================================================================
    
      $result = mysql_query("select message.id from message where uid=\"$from\" order by id desc limit 0,1;");
      if ($result)
      {
	  	  
	  if ($result && mysql_num_rows($result))
	  {
	    $numrows = mysql_num_rows($result);
	    $rowcount = 1;
	    while ($row = mysql_fetch_assoc($result))
	    {
	      while(list($var,$val) = each($row))
	      {
		if ($var == "id") $id = $val;		
	      }
	      ++$rowcount;	
	      $record[] = array('messageid' => $id);
	    }
	  }  	
	  	
	return $record;	
      }	      
      else
      {
	return false;
      }      
      
    }
    
    public function friendsearch($search)
    {
      $result = mysql_query("SELECT name,unique_id from users where name like \"%$search%\" order by name limit 0,10");
      if ($result)
      {
	  	  
	  if ($result && mysql_num_rows($result))
	  {
	    $numrows = mysql_num_rows($result);
	    $rowcount = 1;
	    while ($row = mysql_fetch_assoc($result))
	    {
	      while(list($var,$val) = each($row))
	      {
		if ($var == "name") $name = $val;
		if ($var == "unique_id") $uid = $val;		
	      }
	      ++$rowcount;	
	      $record[] = array($name => $uid);
	    }
	  }  	
	  	
	return $record;	
      }	      
      else
      {
	return false;
      }
    }
    
    public function friendrequestlist($uid)
    {
      $result = mysql_query("select friendreq.ts,friendreq.sid,users.name from friendreq,users where friendreq.sid=users.unique_id and friendreq.rid=\"$uid\" order by friendreq.ts;");
      if ($result)
      {
	  	  
	  if ($result && mysql_num_rows($result))
	  {
	    $numrows = mysql_num_rows($result);
	    $rowcount = 1;
	    while ($row = mysql_fetch_assoc($result))
	    {
	      while(list($var,$val) = each($row))
	      {
		if ($var == "ts") $ts = $val;
		if ($var == "sid") $sid = $val;
		if ($var == "name") $name = $val;
	      }
	      ++$rowcount;	
	      $record[] = array($name => $sid,'ts' => $ts);
	    }
	  }  	
	  	
	return $record;	
      }	      
      else
      {
	return false;
      }
    }    
    
    public function friendlist($uid)
    {
      $ok = false;
      $result = mysql_query("select friend.ts,friend.sid,users.name from friend,users where friend.sid=users.unique_id and friend.rid=\"$uid\" order by friend.ts;");
      if ($result)
      {
	  $ok = true;
	  if ($result && mysql_num_rows($result))
	  {
	    $numrows = mysql_num_rows($result);
	    $rowcount = 1;
	    while ($row = mysql_fetch_assoc($result))
	    {
	      while(list($var,$val) = each($row))
	      {
		if ($var == "ts") $ts = $val;
		if ($var == "sid") $sid = $val;
		if ($var == "name") $name = $val;
	      }
	      ++$rowcount;	
	      $record[] = array($name => $sid,'ts' => $ts);
	    }
	  }  		  		
      }

      $result = mysql_query("select friend.ts,friend.rid,users.name from friend,users where friend.rid=users.unique_id and friend.sid=\"$uid\" order by friend.ts;");
      if ($result)
      {
	  $ok = true;	  
	  if ($result && mysql_num_rows($result))
	  {
	    $numrows = mysql_num_rows($result);
	    $rowcount = 1;
	    while ($row = mysql_fetch_assoc($result))
	    {
	      while(list($var,$val) = each($row))
	      {
		if ($var == "ts") $ts = $val;
		if ($var == "rid") $sid = $val;
		if ($var == "name") $name = $val;
	      }
	      ++$rowcount;	
	      $record[] = array($name => $sid,'ts' => $ts);
	    }
	  }  		  		
      }      
      
      
      if ($ok == true)
      {
	  return $record;
      }
      else
      {
	return false;
      }      
      
    }
    
    public function defriend($to,$from)
    {
      $result = mysql_query("delete from friend where sid=\"$from\" and rid=\"$to\";");     
      $result = mysql_query("delete from friend where sid=\"$to\" and rid=\"$from\";");     
     
      return true;
            
    }
    
    public function friendresponse($to,$from,$action)
    {
      if ($action == 'accept')
      {
	    $result = mysql_query("delete from friendreq where sid=\"$from\" and rid=\"$to\";");        
	    $result = mysql_query("insert into friend (sid,rid) values (\"$from\",\"$to\");");  
	    if ($result) 
	    {	    	    
		return true;
	    }
	    else
	    {
	      return false;
	    }		
	
      }
      else if($action == 'reject')
      {
	    $result = mysql_query("delete from friendreq where sid=\"$from\" and rid=\"$to\";");	    
	    if ($result) 
	    {	    	    
		return true;
	    }
	    else
	    {
	      return false;
	    }	
			
      }
      else
      {
	  return false;
	  
      }
    }
    
    public function friendrequest($from,$to)
    {      
        $result = mysql_query("insert into friendreq (sid,rid) values (\"$from\",\"$to\");");        
        if ($result) 
        {	    	    
	    return true;
        }
        else
        {
	  return false;
	}
    }
    

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        $result = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $result = mysql_query("SELECT email from users WHERE email = '$email'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

}

?>
