<?php

class Player{

    public $login;
    public $table;
    public $idplayer;
    public $coins;
    
    public function __construct($tableID,$login){
        $this->login = $login;
        $this->table = new Table($tableID);
        $this->idplayer = $this->currentPlayerID();
        $this->coins = $this->getPlayerValue('coins');
    }
    
    public function joinTheTable()
    {   
        $this->checkIfDisconnectedPlayerCanJoinAgain();
        
        if($this->table->numberofplayers<5){
            
            $this->table->numberofplayers += 1;

            //InGameWaiting
            $this->table->updateTableSet("P{$this->table->numberofplayers}","{$this->login}.IGW.CANNOTRAISE.0");

            //Curtime() + 10 min 
            $this->table->updateTableSetSql("`P".$this->table->numberofplayers."Timeout`=CURTIME() + 1000");

            $_SESSION['onTable']=true;

            //Add player
            $this->table->updateTableSet("numberofplayers",$this->table->numberofplayers);
            $_SESSION['tableID']=$this->table->tableID;

            header('Location: ../../../game');
        }else{
            header('Location: ../../../menu');
        }
        
    }

    private function checkIfDisconnectedPlayerCanJoinAgain()
    {
        $players = $this->table->getPlayers();
        $P = $this->table->getPlayersLoginChunks($players);
        for($x=1; $x<=5; $x++){
            if($_SESSION['user']==$P[$x]){
                header('Location: ../../../menu');
                exit();
            }
        }
    }

    public function verifyPlayerTurn()
    {
        $playerturn = $this->table->getValueFromTheTable("playerturn");
        if($this->login!=$playerturn){
            echo "It's not your turn";
            exit();
        }
    }

    public function currentPlayerID()
    {
        $idplayer;
        for($x=1; $x<=5; $x++){
            //Pure login
            $plogin = $this->table->getCertainValueChunk("P$x", 0);
            if($this->login == $plogin){
                $idplayer = $x;
                return $idplayer;
            }
        }
    }
    
    public function whichPlayer($login){
        $idplayer;
        for($x=1; $x<=5; $x++){
            $plogin = $this->table->getCertainValueChunk("P$x", 0);
            if($login == $plogin){
                $idplayer = $x;
                return $idplayer;
            }
        }
    }
    
    public function getPlayerValue($value){
        $stmt = $this->table->connect()->prepare("SELECT `$value` FROM `players` WHERE `login`=?");
        $stmt->execute(array($this->login));
        $column = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($column["$value"])){
            $value = $column["$value"];
            return $value;
        }
    }


    public function getPlayerValueById($value,$login){
        $stmt = $this->table->connect()->prepare("SELECT `$value` FROM `players` WHERE `login`=?");
        $stmt->execute(array($login));
        $column = $stmt->fetch(PDO::FETCH_ASSOC);
        $value = $column["$value"];
        return $value;
    }

    public function updateCurrentPlayerSet($column,$value)
    {
        $stmt = $this->table->connect()->prepare("UPDATE `players` SET $column=? WHERE `login`=?");
        $stmt->execute(array($value,$this->login));
    }


    public function updatePlayerSet($column,$value,$login)
    {
        $stmt = $this->table->connect()->prepare("UPDATE `players` SET $column=? WHERE `login`=?");
        $stmt->execute(array($value,$login));
    }

    public function getPlayerLogins()
    {
        $stmt = $this->table->connect()->prepare("SELECT P1,P2,P3,P4,P5 FROM `table` WHERE id=?");
        $stmt->execute(array($this->table->tableID));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);

        for($x=1; $x<=5; $x++){
            $P[$x] = $this->table->getCertainValueChunksByValue($row["P$x"],0);
        }
        return $P;
    }

    public function getPlayersH1()
    {
        $stmt = $this->table->connect()->prepare("SELECT P1H1,P2H1,P3H1,P4H1,P5H1 FROM `table` WHERE id=?");
        $stmt->execute(array($this->table->tableID));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);

        for($x=1; $x<=5; $x++){
            $PH1[$x] = $this->table->getCertainValueChunksByValue($row["P".$x."H1"],3);
        }
        return $PH1;
    }

    
    public function getPlayersH2()
    {
        $stmt = $this->table->connect()->prepare("SELECT P1H2,P2H2,P3H2,P4H2,P5H2 FROM `table` WHERE id=?");
        $stmt->execute(array($this->table->tableID));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        for($x=1; $x<=5; $x++){
            $PH2[$x] = $this->table->getCertainValueChunksByValue($row["P".$x."H2"],3);
        }
        return $PH2;
    }
    
}