<?php

class Signup extends Dbh{

    protected function setUser($uid,$pwd,$email,$coins){
        $stmt =$this->connect()->prepare('INSERT INTO players (`login`,`password`,email,coins)
        VALUES (?,?,?,?)');

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        if(!$stmt->execute(array($uid,$hashedPwd,$email,$coins))){
            $stmt=null;
            header("location: ../../../index.php?error=stmtfailed");
            exit();
        }

        $stmt=null;
    }
    
    protected function checkUser($uid,$email){
        $stmt =$this->connect()->prepare('SELECT `login` FROM players WHERE `login`=? OR email=?');

        if(!$stmt->execute(array($uid,$email))){
            $stmt=null;
            header("location: ../../../index.php?error=stmtfailed");
            exit();
        }

        $resultCheck;
        if($stmt->rowCount()>0){
            $resultCheck=false;
        }else{
            $resultCheck=true;
        }

        return $resultCheck;
    }

}