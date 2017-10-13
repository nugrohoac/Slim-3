<?php

    require_once('config.php');

    $app->get('/buku', function($request, $response, $args){
        echo "Welcome to Book";
    });

    function book(){
        $query = "SELECT * FROM books order by id";
        $result = $mysqli->query($query);
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        return $data;
    };

?>