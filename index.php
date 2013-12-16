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
            $response["user"]["location"] = $user["location"];
            $response["user"]["gender"] = $user["gender"];
            $response["user"]["birthday"] = $user["birthday"];
            $response["user"]["hobby"] = $user["hobby"];
            $response["user"]["datecreated"] = $user["datecreated"];
            $response["user"]["dateupdated"] = $user["dateupdated"];
            $response["user"]["lastlogin"] = $user["lastlogin"];
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
        } else if (!$db->validNickname($nickname)) {
            $response["error"] = 5;
            $response["error_msg"] = "Nickname must be at least 2 characters long";
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
                $response["user"]["location"] = $user["location"];
                $response["user"]["gender"] = $user["gender"];
                $response["user"]["birthday"] = $user["birthday"];
                $response["user"]["hobby"] = $user["hobby"];
                $response["user"]["datecreated"] = $user["datecreated"];
                $response["user"]["dateupdated"] = $user["dateupdated"];
                $response["user"]["lastlogin"] = $user["lastlogin"];
                echo json_encode($response);
            } else {
                // user failed to store
                $response["error"] = 1;
                $response["error_msg"] = "Error occured in Registartion";
                echo json_encode($response);
            }
        }
    } // end of tag register
    else if ($tag == 'getUserStatisticalData') {
        $email = $_POST['email'];
        // get total posts made by a user in all rooms + the number of joined rooms based on the supplied email
        $userGetTotalUserPosts = $db->getTotalUserPosts($email);
        $userGetTotalRoomsJoined = $db->getTotalRoomsJoined($email);
        if ($userGetTotalUserPosts != false && $userGetTotalRoomsJoined != false) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["getUserStatisticalData"]["getTotalPosts"] = $userGetTotalUserPosts["getTotalPosts"];
            $response["getUserStatisticalData"]["getTotalRoomsJoined"] = $userGetTotalRoomsJoined["getTotalRoomsJoined"];
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem getting the statistical data";
            echo json_encode($response);
        }
    }// end of tag getUserStatisticalData
    else if ($tag == 'updateUserData') {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $location = $_POST['location'];
        $gender = $_POST['gender'];
        $birthday = $_POST['birthday'];
        $hobby = $_POST['hobby'];
        // get a confirmation that the data has been updated
        $userUpdateData = $db->updateUserData($email, $name, $location, $gender, $birthday, $hobby);
        if ($userUpdateData != false) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["user"]["email"] = $userUpdateData["email"];
            $response["user"]["name"] = $userUpdateData["name"];
            $response["user"]["location"] = $userUpdateData["location"];
            $response["user"]["gender"] = $userUpdateData["gender"];
            $response["user"]["birthday"] = $userUpdateData["birthday"];
            $response["user"]["hobby"] = $userUpdateData["hobby"];
            $response["user"]["dateupdated"] = $userUpdateData["dateupdated"];
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem updating user";
            echo json_encode($response);
        }
    }// end of tag updateUserData
    else if ($tag == 'resetpassword') {
        // Request type is Register new user
        $email = $_POST['email'];
        $oldpassword = $_POST['oldpassword'];
        $newpassword = $_POST['newpassword'];

        // check if the new password is complex enough
        if (!$db->validPassword($newpassword)) {
            $response["error"] = 4;
            $response["error_msg"] = "Password too weak, (min. six characters)";
            echo json_encode($response);
        } else {
            // reset pass
            $resetpass = $db->resetPassword($email, $oldpassword, $newpassword);
            if ($resetpass) {
                // pass reset successful
                $response["success"] = 1;
                $response ["PasswordResetResult"] = "Successful";
                $response["user"]["email"] = $resetpass["email"];
                echo json_encode($response);
            } else {
                // pass reset failure
                $response["error"] = 1;
                $response["error_msg"] = "Error occured in password reset";
                echo json_encode($response);
            }
        }
    } // end of tag resetpassword
    else if ($tag == 'getMainRooms') {

        $getmainrooms = $db->getMainRooms();
        if ($getmainrooms) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["MainRooms"] = $getmainrooms;
            echo json_encode($response);
        } else {
            // get main rooms failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting main rooms";
            echo json_encode($response);
        }
    } // end of tag getMainRooms
    else if ($tag == 'getRoomMembership') {
        $email = $_POST['email'];
        $getroommembership = $db->getRoomMembership($email);
        if ($getroommembership) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["RoomMembership"] = $getroommembership;
            echo json_encode($response);
        } else {
            // getting room membership failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting room membership data";
            echo json_encode($response);
        }
    } // end of tag getRoomMembership
    else if ($tag == 'createroom') {

        $name = $_POST['name'];
        $owner = $_POST['owner'];
        $parentroom = $_POST['parentroom'];

        // check if room name is complex enough
        if (!$db->validRoomName($name)) {
            //  room name not complex enough
            $response["error"] = 2;
            $response["error_msg"] = "Room name must be at least 2 characters";
            echo json_encode($response);
        } else {
            // store room
            $room = $db->createRoom($name, $owner, $parentroom);
            if ($room) {
                // room created successfully
                $response["success"] = 1;
                $response["room"]["name"] = $room["name"];
                $response["room"]["owner"] = $room["owner"];
                $response["room"]["parentroom"] = $room["parentroom"];
                $response["room"]["datecreated"] = $room["datecreated"];
                echo json_encode($response);
            } else {
                // room failed to store
                $response["error"] = 1;
                $response["error_msg"] = "Error occured in room creation";
                echo json_encode($response);
            }
        }
    } // end of tag createroom
    else if ($tag == 'getSubRooms') {
        $parentroom = $_POST['parentroom'];

        $getsubrooms = $db->getSubRooms($parentroom);
        if ($getsubrooms) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["SubRooms"] = $getsubrooms;
            echo json_encode($response);
        } else {
            // get sub rooms failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting sub rooms";
            echo json_encode($response);
        }
    } // end of tag getsubrooms
    else if ($tag == 'joinRoom') {
        $email = $_POST['email'];
        $roomname = $_POST['roomname'];
        //make a record based on email and room name
        $joinRoom = $db->joinRoom($email, $roomname);
        if ($joinRoom) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["user"]["user"] = $joinRoom["user"];
            $response["room"]["room"] = $joinRoom["room"];
            $response["datecreated"] = $joinRoom["datecreated"];
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem joining the room";
            echo json_encode($response);
        }
    } // end of tag joinRoom
    else if ($tag == 'leaveRoom') {
        $email = $_POST['email'];
        $roomname = $_POST['roomname'];
        //remove the userroom record based on an email and room name
        $leaveRoom = $db->leaveRoom($email, $roomname);
        if ($leaveRoom) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["success"]["success"] = $leaveRoom;
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem leaving the room";
            echo json_encode($response);
        }
    } // end of tag leaveRoom
    else if ($tag == 'searchRooms') {
        $roomname = $_POST['roomname'];

        $getrooms = $db->searchRooms($roomname);
        if ($getrooms) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["SearchRooms"] = $getrooms;
            echo json_encode($response);
        } else {
            // get room list failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting the room list";
            echo json_encode($response);
        }
    } //end of tag searchRoom
    else if ($tag == 'searchFriend') {
        $useremail = $_POST['useremail'];

        $getusers = $db->searchUser($useremail);
        if ($getusers) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["SearchUsers"] = $getusers;
            echo json_encode($response);
        } else {
            // get user list failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting the user list";
            echo json_encode($response);
        }
    }//end of tag searchFriend
    else if ($tag == 'inviteFriend') {
        $from = $_POST['from'];
        $to = $_POST['to'];
        //make a record based on from and to with the default status of pending
        $inviteFriend = $db->inviteUserToFriend($from, $to);
        if ($inviteFriend) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["friendreq"]["from"] = $inviteFriend["from"];
            $response["friendreq"]["to"] = $inviteFriend["to"];
            $response["friendreq"]["status"] = $inviteFriend["status"];
            $response["friendreq"]["datacreated"] = $inviteFriend["datecreated"];
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem making the friend request";
            echo json_encode($response);
        }
    } // end of tag joinRoom
    else if ($tag == 'getFriends') {
        $user = $_POST['user1'];
        $getfriends = $db->getFriends($user);
        if ($getfriends) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["GetFriends"] = $getfriends;
            echo json_encode($response);
        } else {
            // get user list failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting the friend list";
            echo json_encode($response);
        }
    }// end of tag getFriends
    else if ($tag == 'removeFriend') {
        $user1 = $_POST['user1'];
        $user2 = $_POST['user2'];
        //remove the friend record
        $removeFriend = $db->removeFriend($user1, $user2);
        if ($removeFriend) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["success"]["success"];
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem removing the friend";
            echo json_encode($response);
        }
    }//end of tag removeFriend
    else if ($tag == 'getFriendRequests') {
        $to = $_POST['to'];
        $getfriendrequests = $db->getFriendRequests($to);
        if ($getfriendrequests) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["GetFriendRequests"] = $getfriendrequests;
            echo json_encode($response);
        } else {
            // get user list failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting the friend requests";
            echo json_encode($response);
        }
    }//end of tag getFriendRequests
    else if ($tag == 'acceptFriendRequest') {
        $from = $_POST['from'];
        $to = $_POST['to'];
        $acceptfriendrequest = $db->acceptFriendRequest($from, $to);
        if ($acceptfriendrequest) {
            //the query was successful + form json object
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            // get user list failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in accepting the friend request";
            echo json_encode($response);
        }
    }//end of tag acceptFriendRequest
    else if ($tag == 'declineFriendRequest') {
        $from = $_POST['from'];
        $to = $_POST['to'];
        $declinefriendrequest = $db->declineFriendRequest($from, $to);
        if ($declinefriendrequest) {
            //the query was successful + form json object
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            // get user list failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in declining the friend request";
            echo json_encode($response);
        }
    }//end of tag declineFriendRequest
    else if ($tag == 'getFriendRequestStatuses') {
        $from = $_POST['from'];
        $getfriendreqstatuses= $db->getFriendRequestStatuses($from);
        if ($getfriendreqstatuses) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["GetFriendRequestStatuses"] = $getfriendreqstatuses;
            echo json_encode($response);
        } else {
            // get requests  failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting friend request statutses";
            echo json_encode($response);
        }
    }//end of tag getFriendRequestStatuses
        else if ($tag == 'getPosts') {
        $roomname = $_POST['roomname'];
        $getposts= $db->getPosts($roomname);
        if ($getposts) {
            //the query was successful + form json object
            $response["success"] = 1;
            $response["GetPosts"] = $getposts;
            echo json_encode($response);
        } else {
            // get requests  failed
            $response["error"] = 1;
            $response["error_msg"] = "Error occured in getting the forum posts";
            echo json_encode($response);
        }
    }//end of tag getPosts
        else if ($tag == 'makePost') {
        $user = $_POST['user'];
        $room = $_POST['room'];
        $post = $_POST['post'];
        //make a post entry based on a specific room and a user
        $makePost = $db->makePost($user, $room, $post);
        if ($makePost) {
            // the query was ok
            // echo json with success = 1
            $response["success"] = 1;
            $response["post"]["user"] = $makePost["user"];
            $response["post"]["room"] = $makePost["room"];
            $response["post"]["post"] = $makePost["post"];
            $response["post"]["datacreated"] = $makePost["datecreated"];
            echo json_encode($response);
        } else {
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "There was a problem submitin the post";
            echo json_encode($response);
        }
    }
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
