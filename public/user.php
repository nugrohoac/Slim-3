<?php
    //jwt
    use \Firebase\JWT\JWT;
    //connect database, config secret key
    require_once('../app/api/config.php');
    require_once('email.php'); //sending email
    
    //create user
    function addUser($username, $name, $password, $email, $role, $address, $no_telepon){
        $connect = connect();
        $isValidate = 0;
        $query = "INSERT INTO user (username,name,password,email,role,isValidate,address,no_telepon) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("ssssiiss",$username,$name,$password,$email,$role,$isValidate,$address,$no_telepon);
        if($stmt->execute()){
            //sending email to validation
            sendEmail($email,$username);
            $time = time(); //time now
            $key = secretKey();
            $token = array(
                "iss" => "yippytech.com",
                "iat" => $time,
                "exp" => $time + (3600 * 50),
                "data" => [
                    "username" => $username,
                    "name" => $name,
                    "email"=> $email
                ]
            );
            //create JWT token
            $jwt = JWT::encode($token, $key, "HS256");        
            return "sukses";
        } else {
            return "failed";
        }
    }

    function login($username, $password){
        $connect = connect();
        $query = "SELECT user_id, username, name, role, isValidate from user WHERE username = '$username' AND password = '$password'";
        $result = $connect->query($query);
        $data['data'][] = $result->fetch_assoc();
        return $data;
        //echo $username;
    }

?>