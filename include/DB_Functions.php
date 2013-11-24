<?php

class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'connection.inc.php';
        //connect to database
        $this->conn = dbConnect('write');
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new user +
     * returns user details
     */
    public function registerUser($email, $nickname, $password) {
        $hash = $this->hashSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        //generate the timestamp to update the dateupdated field in the mysql database via the prepared statement
        $date = new DateTime();
        $datecreated = $date->format('Y-m-d H:i:s');
        $sql = 'INSERT INTO user ( email, nickname, password, salt, datecreated)
          VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->conn->stmt_init();
        $stmt = $this->conn->prepare($sql);
        // bind parameters and insert the details into the database
        $stmt->bind_param('sssis', $email, $nickname, $encrypted_password, $salt, $datecreated);
        $stmt->execute();
        // check for successful store
        if ($stmt->affected_rows == 1) {
            // get user details 
            $sql = "SELECT * FROM user WHERE email =\"$email\"";
            // return user details
            $result = $this->conn->query($sql) or die($this->conn->error);
            return $result->fetch_assoc();
            //return mysql_fetch_array($result);
        } else {
            echo 'Sorry, there was a problem with the database.';
        }
    }

    /**
     * Get user by email and password
     */
    public function loginUser($email, $password) {
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = $this->conn->query($sql) or die($this->conn->error);
        $numRows = $result->num_rows;
        // check for result 
        if ($numRows > 0) {
            $result = $result->fetch_assoc();
            $salt = $result['salt'];
            $encrypted_password = $result['password'];
            $hash = $this->checkhashSHA($salt, $password);
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
     * Check if user is already in the system
     */
    public function isUserExisted($email) {
        $sql = "SELECT email from user WHERE email = '$email'";
        $result = $this->conn->query($sql) or die($this->conn->error);
        $numRows = $result->num_rows;
        if ($numRows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }

    /**
     * Get statistical data about the number of posts made by a user
     */
    public function getTotalUserPosts($email) {
        $sql = "SELECT COUNT(*) AS getTotalPosts FROM post WHERE USER = '$email'";
        $result = $this->conn->query($sql) or die($this->conn->error);
        $numRows = $result->num_rows;
        if ($numRows > 0) {
            $result = $result->fetch_assoc();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Get statistical data about the number of room joind by a user 
     */
    public function getTotalRoomsJoined($email) {
        $sql = "SELECT COUNT(*) AS getTotalRoomsJoined FROM userroom WHERE USER ='$email'";
        $result = $this->conn->query($sql) or die($this->conn->error);
        $numRows = $result->num_rows;
        if ($numRows > 0) {
            $result = $result->fetch_assoc();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Storing user data +
     * returns user data
     */
    public function updateUserData($email, $name, $location, $gender, $birthday, $hobby) {
        //generate the timestamp to update the dateupdated field in the mysql database via the prepared statement
        $date = new DateTime();
        $dateupdated = $date->format('Y-m-d H:i:s');
        $sql = "UPDATE user  SET name = ?, location = ?, gender = ?, birthday = ?, hobby = ?, dateupdated = ? WHERE email = ?";
        $stmt = $this->conn->stmt_init();
        $stmt = $this->conn->prepare($sql);
        // bind parameters and insert the details into the database
        $stmt->bind_param('sssssss', $name, $location, $gender, $birthday, $hobby, $dateupdated, $email);
        $stmt->execute();
        // check for successful updated
        if ($stmt->affected_rows == 1) {
            // get the updated user details 
            $sql = "SELECT email, name, location, gender, birthday, hobby, dateupdated FROM user WHERE email =\"$email\"";
            // return user details
            $result = $this->conn->query($sql) or die($this->conn->error);
            return $result->fetch_assoc();
            //return mysql_fetch_array($result);
        } else {
            echo 'Sorry, there was a problem with the database.';
        }
    }

    /**
     * Reset user password based on old + new pass
     */
    public function resetPassword($email, $oldpassword, $newpassword) {
        $sql = "SELECT email, password, salt  FROM user WHERE email = '$email'";
        $result = $this->conn->query($sql) or die($this->conn->error);
        $numRows = $result->num_rows;
        // check for result 
        if ($numRows > 0) {
            $result = $result->fetch_assoc();
            $salt = $result['salt'];
            $encrypted_password = $result['password'];
            $hash = $this->checkhashSHA($salt, $oldpassword);
            // check whether the old password matches
            if ($encrypted_password == $hash) {
                // if the old password matches update the password with the new password
                $newhash = $this->hashSHA($newpassword);
                $encrypted_newpassword = $newhash["encrypted"]; // encrypted password
                $newsalt = $newhash["salt"]; // salt
                //generate the timestamp to update the dateupdated field in the mysql database via the prepared statement
                $date = new DateTime();
                $dateupdated = $date->format('Y-m-d H:i:s');
                $sql = "UPDATE user SET password = ?, salt = ?, dateupdated = ? WHERE email = ?";
                $stmt = $this->conn->stmt_init();
                $stmt = $this->conn->prepare($sql);
                // bind parameters and insert the details into the database
                $stmt->bind_param('siss', $encrypted_newpassword, $newsalt, $dateupdated, $email);
                $stmt->execute();
                if ($stmt->affected_rows == 1) {
                    // get user details 
                    $sql = "SELECT email, dateupdated FROM user WHERE email =\"$email\"";
                    // return user details
                    $result = $this->conn->query($sql) or die($this->conn->error);
                    return $result->fetch_assoc();
                } else {
                    echo 'Sorry, there was a problem with the database.';
                }

            }
        } else {
            // There was a problem --> the submitted old password did not match the one in the db 
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSHA($password) {

        $salt = time();
        $encrypted = sha1($password . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSHA($salt, $password) {

        $hash = sha1($password . $salt);

        return $hash;
    }

    /**
     * check is the pasword is at leaset 6 characters
     */
    function validPassword($password) {
        $ok = true;
        if (strlen($password) < 6)
            $ok = false;

        return $ok;
    }

    /**
     * check is the nickname is at least 2 characters
     */
    function validNickname($nickname) {
        $ok = true;
        if (strlen($nickname) < 2)
            $ok = false;

        return $ok;
    }

    /**
      Validate an email address.
      Provide email address (raw input)
      Returns true if the email address has the email
      address format and the domain exists.

      Stole this somewhere on the Internet :)
     */
    function validEmail($email) {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if
            (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                // character not valid in local part unless 
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain, "MX") ||
                    checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }

    public function getwall($uid) {
        //  ===============================================================================
        //  ===============================================================================
        //  Change this when we have friends functionality working
        //  ===============================================================================
        //  ===============================================================================

        $result = mysql_query("select users.name,message.message from users,message where message.uid=users.unique_id order by message.ts;");
        if ($result) {

            if ($result && mysql_num_rows($result)) {
                $numrows = mysql_num_rows($result);
                $rowcount = 1;
                while ($row = mysql_fetch_assoc($result)) {
                    while (list($var, $val) = each($row)) {
                        if ($var == "name")
                            $name = $val;
                        if ($var == "message")
                            $message = $val;
                    }
                    ++$rowcount;
                    $record[] = array($name => $message);
                }
            }

            return $record;
        }
        else {
            return false;
        }
    }

    public function latestmessageid($from) {
        //  ===============================================================================
        //  ===============================================================================
        //  Change this when we have friends functionality working
        //  ===============================================================================
        //  ===============================================================================

        $result = mysql_query("select message.id from message where uid=\"$from\" order by id desc limit 0,1;");
        if ($result) {

            if ($result && mysql_num_rows($result)) {
                $numrows = mysql_num_rows($result);
                $rowcount = 1;
                while ($row = mysql_fetch_assoc($result)) {
                    while (list($var, $val) = each($row)) {
                        if ($var == "id")
                            $id = $val;
                    }
                    ++$rowcount;
                    $record[] = array('messageid' => $id);
                }
            }

            return $record;
        }
        else {
            return false;
        }
    }

    public function friendsearch($search) {
        $result = mysql_query("SELECT name,unique_id from users where name like \"%$search%\" order by name limit 0,10");
        if ($result) {

            if ($result && mysql_num_rows($result)) {
                $numrows = mysql_num_rows($result);
                $rowcount = 1;
                while ($row = mysql_fetch_assoc($result)) {
                    while (list($var, $val) = each($row)) {
                        if ($var == "name")
                            $name = $val;
                        if ($var == "unique_id")
                            $uid = $val;
                    }
                    ++$rowcount;
                    $record[] = array($name => $uid);
                }
            }

            return $record;
        }
        else {
            return false;
        }
    }

    public function friendrequestlist($uid) {
        $result = mysql_query("select friendreq.ts,friendreq.sid,users.name from friendreq,users where friendreq.sid=users.unique_id and friendreq.rid=\"$uid\" order by friendreq.ts;");
        if ($result) {

            if ($result && mysql_num_rows($result)) {
                $numrows = mysql_num_rows($result);
                $rowcount = 1;
                while ($row = mysql_fetch_assoc($result)) {
                    while (list($var, $val) = each($row)) {
                        if ($var == "ts")
                            $ts = $val;
                        if ($var == "sid")
                            $sid = $val;
                        if ($var == "name")
                            $name = $val;
                    }
                    ++$rowcount;
                    $record[] = array($name => $sid, 'ts' => $ts);
                }
            }

            return $record;
        }
        else {
            return false;
        }
    }

    public function friendlist($uid) {
        $ok = false;
        $result = mysql_query("select friend.ts,friend.sid,users.name from friend,users where friend.sid=users.unique_id and friend.rid=\"$uid\" order by friend.ts;");
        if ($result) {
            $ok = true;
            if ($result && mysql_num_rows($result)) {
                $numrows = mysql_num_rows($result);
                $rowcount = 1;
                while ($row = mysql_fetch_assoc($result)) {
                    while (list($var, $val) = each($row)) {
                        if ($var == "ts")
                            $ts = $val;
                        if ($var == "sid")
                            $sid = $val;
                        if ($var == "name")
                            $name = $val;
                    }
                    ++$rowcount;
                    $record[] = array($name => $sid, 'ts' => $ts);
                }
            }
        }

        $result = mysql_query("select friend.ts,friend.rid,users.name from friend,users where friend.rid=users.unique_id and friend.sid=\"$uid\" order by friend.ts;");
        if ($result) {
            $ok = true;
            if ($result && mysql_num_rows($result)) {
                $numrows = mysql_num_rows($result);
                $rowcount = 1;
                while ($row = mysql_fetch_assoc($result)) {
                    while (list($var, $val) = each($row)) {
                        if ($var == "ts")
                            $ts = $val;
                        if ($var == "rid")
                            $sid = $val;
                        if ($var == "name")
                            $name = $val;
                    }
                    ++$rowcount;
                    $record[] = array($name => $sid, 'ts' => $ts);
                }
            }
        }


        if ($ok == true) {
            return $record;
        } else {
            return false;
        }
    }

    public function defriend($to, $from) {
        $result = mysql_query("delete from friend where sid=\"$from\" and rid=\"$to\";");
        $result = mysql_query("delete from friend where sid=\"$to\" and rid=\"$from\";");

        return true;
    }

    public function friendresponse($to, $from, $action) {
        if ($action == 'accept') {
            $result = mysql_query("delete from friendreq where sid=\"$from\" and rid=\"$to\";");
            $result = mysql_query("insert into friend (sid,rid) values (\"$from\",\"$to\");");
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else if ($action == 'reject') {
            $result = mysql_query("delete from friendreq where sid=\"$from\" and rid=\"$to\";");
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function friendrequest($from, $to) {
        $result = mysql_query("insert into friendreq (sid,rid) values (\"$from\",\"$to\");");
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}

?>
