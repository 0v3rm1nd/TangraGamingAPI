<?php

if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // get tag 
    $tag = $_POST['tag'];

    // include database handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();

    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);

    // check for tag type based on the submited data from the Anroid App
    if ($tag == 'login') {
        // Request type is check Login
        $email = $_POST['email'];
        $password = $_POST['password'];

        // check for user
        $user = $db->loginUser($email, $password);
        if ($user != false) {
            // user found
            // echo json with success = 1
            $response["success"] = 1;
            $response["user"]["email"] = $user["email"];
            $response["user"]["nickname"] = $user["nickname"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["datecreated"] = $user["datecreated"];
            $response["user"]["dateupdated"] = $user["dateupdated"];
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "Incorrect email or password!";
            echo json_encode($response);
        }
    }  //END OF Tag login 
    else if ($tag == 'register') {
        // Request type is Register new user
        $email = $_POST['email'];
        $nickname = $_POST['nickname'];
        $password = $_POST['password'];

        // check if user is already existed
        if ($db->isUserExisted($email)) {
            // user is already existed - error response
            $response["error"] = 2;
            $response["error_msg"] = "User already existed";
            echo json_encode($response);
        } else if (!$db->validEmail($email)) {
            $response["error"] = 3;
            $response["error_msg"] = "E-mail address not valid";
            echo json_encode($response);
        } else if (!$db->validPassword($password)) {
            $response["error"] = 4;
            $response["error_msg"] = "Password too weak, (min. six characters)";
            echo json_encode($response);
        } else {
            // store user
            $user = $db->registerUser($email, $nickname, $password);
            if ($user) {
                // user stored successfully
                $response["success"] = 1;
                $response["user"]["email"] = $user["email"];
                $response["user"]["nickname"] = $user["nickname"];
                $response["user"]["name"] = $user["name"];
                $response["user"]["datecreated"] = $user["datecreated"];
                $response["user"]["dateupdated"] = $user["dateupdated"];
                echo json_encode($response);
            } else {
                // user failed to store
                $response["error"] = 1;
                $response["error_msg"] = "Error occured in Registartion";
                echo json_encode($response);
            }
        }
    }  // end of tag register 
    //End of login functionality 
    //Other part of the system
    else if ($tag == 'getwall') {
        $uid = $_POST['from'];
        $wall = $db->getwall($uid);
        if ($wall) {
            echo json_encode($wall);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "No messages";
            echo json_encode($response);
        }
    } else if ($tag == 'postmessage') {
        $response["error"] = 1;
        $response["error_msg"] = "No one wants to read your message";
        echo json_encode($response);
    } else if ($tag == 'friendsearch') {
        $search = $_POST['search'];
        $friendlist = $db->friendsearch($search);
        if ($friendlist) {
            echo json_encode($friendlist);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "Sorry no results";
            echo json_encode($response);
        }
    } else if ($tag == 'friendrequest') {
        $from = $_POST['from'];
        $to = $_POST['to'];

        $friendrequest = $db->friendrequest($from, $to);
        if ($friendrequest) {
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "Request not accepted";
            echo json_encode($response);
        }
    } else if ($tag == 'viewfriendrequest') {
        $uid = $_POST['from'];

        $friendrequestlist = $db->friendrequestlist($uid);
        if ($friendrequestlist) {
            echo json_encode($friendrequestlist);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "No friend requests at this time";
            echo json_encode($response);
        }
    } else if ($tag == 'friendrequestresponse') {
        $to = $_POST['to'];
        $from = $_POST['from'];
        $action = $_POST['action'];

        $friendresponse = $db->friendresponse($to, $from, $action);
        if ($friendresponse) {
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "Action could not be completed";
            echo json_encode($response);
        }
    } else if ($tag == 'friendlist') {
        $uid = $_POST['from'];
        $friendlist = $db->friendlist($uid);
        if ($friendlist) {
            echo json_encode($friendlist);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "You have no registered friends";
            echo json_encode($response);
        }
    } else if ($tag == 'defriend') {
        $from = $_POST['from'];
        $to = $_POST['to'];
        $defriend = $db->defriend($to, $from);
        if ($defriend) {
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "Action completed with error";
            echo json_encode($response);
        }
    } else if ($tag == 'latestmessageid') {
        $from = $_POST['from'];
        $latestmessageid = $db->latestmessageid($from);
        if ($latestmessageid) {
            echo json_encode($latestmessageid);
        } else {
            $response["error"] = 1;
            $response["error_msg"] = "Action completed with error";
            echo json_encode($response);
        }
    } else {
        echo "Invalid Request";
    }
}  // END of $_POST  
else {
    echo "Access Denied";
}
?>
