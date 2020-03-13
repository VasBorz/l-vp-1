<?php
function queryDb($conn,$sql,$bind) {
    if(preg_match('/^INSERT/',$sql)){
        try {
            $rs = $conn->prepare($sql);
            $rs->execute($bind);
        }
        catch (Exception $e) {
            die("Oh noes! There's an error in the query!");
        }
    }
    if(preg_match('/^SELECT/',$sql)){
        try {
            $rs = $conn->prepare($sql);
            $rs->execute($bind);
            return $rs->fetchAll();
        }
        catch (Exception $e) {
            die("Oh noes! There's an error in the query!");
        }
    }
}