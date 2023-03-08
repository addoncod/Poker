<?php

class Login extends Dbh{

    protected function getUser($uid,$pwd){
        $stmt =$this->connect()->prepare('SELECT `password` FROM players WHERE `login`=?
        OR email=?');

        if(!$stmt->execute(array($uid,$pwd))){
            $stmt=null;
            header("location: ../../../index.php?error=stmtfailed");
            exit();
        }

        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($pwdHashed)==0){
            $stmt=null;
            header("Location: ../../../index.php?error=usernotfound");
            exit();
        }
        
        $checkPwd = password_verify($pwd, $pwdHashed[0]["password"]);

        if($checkPwd==false){
            $stmt=null;
            header("Location: ../../../index.php?error=wrongpassword");
            exit();
        }else{
            $stmt =$this->connect()->prepare('SELECT * FROM players WHERE `login`=?
            OR email=? AND `password`=?');
            
            if(!$stmt->execute(array($uid,$uid,$pwd))){
                $stmt=null;
                header("location: ../../../index.php?error=stmtfailed");
                exit();
            }

            if($stmt->rowCount()==0){
                $stmt=null;
                header("Location: ../../../index.php?error=usernotfound");
                exit();
            }

            $user=$stmt->fetchAll(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION['user']=$user[0]['login'];
            $_SESSION['coins']=$user[0]['coins'];
            $_SESSION['loggedin']=true;
            header('Location: menu');
        }

        $stmt=null;
    }


}