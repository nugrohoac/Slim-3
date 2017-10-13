<?php
//create conncection
function connect(){
    $host = "localhost";
    $user = "root";
    $pass = "nug123ac";
    $db_name = "digitaltani";

    $con = new mysqli($host, $user, $pass, $db_name);

    return $con;
}

//secretKey JWT
function secretKey(){
    return "digitaltani";
}

?>