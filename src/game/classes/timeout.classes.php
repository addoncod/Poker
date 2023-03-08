<?php

class Timeout{
    
    private $table;
    private $player;

    public function __construct($tableID,$login){
        $this->table = new Table($tableID);
        $this->player = new Player($tableID,$login);
    }
    
    public function inGameCheckTimeout()
    {
        $this->checkTimeout($this->table->numberofplayers,$this->table->tableID);
    }

    public function menuCheckTimeout()
    {
        $numberofplayersT = $this->getNumberOfPlayersFromTables(2);

        for($x=1; $x<=2; $x++){
            $this->checkTimeout($numberofplayersT[$x],$x);
        }
    }

    public function increaseTimeout()
    {
        $playerid = $this->player->whichPlayer($this->player->login);
        $this->table->updateTableSetSql("`P".$playerid."Timeout`=CURTIME() + 1000");
    }

    private function checkTimeout($numberofplayers,$tableID)
    {
        $datetime = new DateTime(date("H:i:s"));
        for($x=1; $x<=$numberofplayers; $x++){
            
            //timeOut
            $to = $this->table->getValueFromTheTableById("P".$x."Timeout",$tableID);
            $timeout = DateTime::CreateFromFormat("H:i:s",$to);
            
            $difference = $datetime->diff($timeout);

            if($datetime>$timeout){
                $isgame = $this->table->getValueFromTheTableById("isgame",$tableID);
                if($isgame==1){
                    $timeoutlogin = $this->table->getCertainValueChunkById("P$x",0,$tableID);
                    $this->table->updateTableSet("P$x","$timeoutlogin.IGFd.CANNOTRAISE.0");
                    if($numberofplayers!=0){
                        $this->table->updateTableSet("numberofplayers",$numberofplayers-1);
                    }
                }else{
                    $this->table->updateTableSet("P$x",NULL);
                    $this->table->updateTableSet("numberofplayers",$numberofplayers-1);
                }
                
            }
        }
    }
    
    private function getNumberOfPlayersFromTables($howmanytables)
    {
        for($x=1; $x<=$howmanytables; $x++){
            $numberofplayersT[$x] = $this->table->getValueFromTheTableById("numberofplayers",$x);
        }
        return $numberofplayersT;
    }
}