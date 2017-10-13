<?php
    //rewuire
    require_once('../app/api/config.php');
    function allOperasiPasar(){
        $connect = connect();
        $query = "SELECT * FROM operasipasar";
        $result = $connect->query($query);
        $vote = false;
        $data['data']=[];
        while($row = $result->fetch_assoc()){
            $data['data'][] = $row;
                $operasipasar_id = $row['operasipasar_id'];
                $query2 = "SELECT * FROM pendukung WHERE operasipasar_id = '$operasipasar_id'";
                $pendukung = $connect->query($query2);
                while($row2 = $pendukung->fetch_assoc())
        }
        return $data;
        //    return $data;
        
    }

?>