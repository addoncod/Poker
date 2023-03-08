<?php

class Disconnect{

    private $player;
    private $table;
    private $round;
    private $igstatus;

    public function __construct($tableID,$login){
        $this->player = new Player($tableID,$login);
        $this->table = new Table($tableID);
        $this->round = new Round($tableID,$login);
        $this->igstatus = $this->table->getCertainValueChunk("P{$this->player->idplayer}",1);
    }

    public function validDisconnectPlayer()
    {
        if($this->igstatus != "IGW"){

            $this->checkIfOutgoingPlayerHaveLoop();

            $igscounter = array(0,0,array(0,null),0);
            $this->countPlayersInGameStatus($igscounter);

            $changeturn=true;
            $this->disconnectIgfdPlayers($igscounter,$changeturn);
            $this->assignThePotToTheLastIgtPlayerCase($igscounter,$changeturn);
            $this->clearTableIfEveryoneLeft($igscounter,$changeturn);

            $this->changeTurnIfOutgoingPlayerHadATurn($changeturn);

        }else{
            $this->disconnectIgwPlayer();
        }

        $this->unsetTableSession();

        header('Location: ../../../menu');
    }

    private function checkIfOutgoingPlayerHaveLoop()
    {
        $playerrate = $this->table->getCertainValueChunk("P{$this->player->idplayer}",3);
        $loopcheck = $this->table->getCertainValueChunk("P{$this->player->idplayer}",4);

        if(empty($loopcheck)){
            $this->table->updateTableSet("P{$this->player->idplayer}","{$this->player->login}.IGFd.CANNOTRAISE.$playerrate");
        }else if($loopcheck=="LOOP" || $loopcheck=="LOOPbb"){
            $this->table->updateTableSet("P{$this->player->idplayer}","{$this->player->login}.IGFd.CANNOTRAISE.$playerrate.LOOP");
        }
    }

    private function countPlayersInGameStatus(&$igscounter)
    {
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $xigstatus = $this->table->getCertainValueChunk("P$x",1);
            switch($xigstatus){
                case "IGFd";
                    $igscounter[0]++;
                break;
                case "IGF";
                    $igscounter[1]++;
                break;
                case "IGT";
                    $igscounter[2][0]++;
                    $igscounter[2][1]=$x;
                break;
                case "IGW";
                    $igscounter[3]++;
                break;
            }
        }
    }
                     
    private function disconnectIgfdPlayers($igscounter,&$changeturn)
    {
        if(($igscounter[2][0]==1 && $igscounter[0]==$this->table->numberofplayers-1) || ($igscounter[3]>0 && $igscounter[0]==$this->table->numberofplayers-$igscounter[3])){
            if($igscounter[2][0]==1){
                
                //Winner login
                $lastigtlogin = $this->table->getCertainValueChunk("P{$igscounter[2][1]}",0);

                $pot = $this->table->getValueFromTheTable("pot");
                $coins = $this->player->getPlayerValueById("coins",$lastigtlogin);

                $this->player->updatePlayerSet("coins",$coins+$pot,$lastigtlogin);

                $this->table->updateTableSet("P{$igscounter[2][1]}","$lastigtlogin.IGW.CANNOTRAISE.0");
            }

            for($i=1; $i<=$this->table->numberofplayers; $i++){
                $ingstatus = $this->table->getCertainValueChunk("P$i",1);
                if($ingstatus=="IGFd"){
                    $this->table->updateTableSet("P$i",NULL);
                }
            }
            $changeturn = false;

            $this->table->resetTableAndDeleteDisconnectedPlayers($igscounter[0]);
              
        }
    }

    private function assignThePotToTheLastIgtPlayerCase($igscounter,&$changeturn)
    {
        if($igscounter[2][0]==1 && $igscounter[1]>0){

            //Winner login
            $lastigtlogin = $this->table->getCertainValueChunk("P{$igscounter[2][1]}",0);

            $pot = $this->table->getValueFromTheTable("pot");

            //Winner login management
            $coins = $this->player->getPlayerValueById("coins",$lastigtlogin);
            $this->player->updatePlayerSet("coins",$coins+$pot,$lastigtlogin);

            $this->table->updateTableSet("P{$this->player->idplayer}",null);
            $this->table->updateTableSet("numberofplayers",$this->table->numberofplayers-1);

            //Set new game
            for($i=1; $i<=$this->table->numberofplayers; $i++){
                if($i!=$this->player->idplayer){
                    $ilogin = $this->table->getCertainValueChunk("P$i",0);
                    $this->table->updateTableSet("P$i","$ilogin.IGW.CANNOTRAISE.0");
                }else{
                    continue;
                }
            }
            $changeturn = false;

            $this->table->resetTableForTheNextGameWithDSbBB();
        }
    }

    private function clearTableIfEveryoneLeft($igscounter,&$changeturn)
    {
        if($igscounter[0]==$this->table->numberofplayers){
            $this->table->clearTable();
            $changeturn = false;
        }
    }

    private function changeTurnIfOutgoingPlayerHadATurn($changeturn)
    {
        if($changeturn == true){
            $playerturn = $this->table->getValueFromTheTable("playerturn");
            if($this->player->login==$playerturn){
                $this->round->changeTurn();
            }
        }
    }

    public function disconnectIgwPlayer()
    {
        $this->table->updateTableSet("P{$this->player->idplayer}",null);
            if($this->table->numberofplayers==1){
                $this->table->clearTable();
            }else{
                $this->table->updateTableSet("numberofplayers",$this->table->numberofplayers-1);
            }
    }

    private function unsetTableSession()
    {
        unset($_SESSION['PH1']);
        unset($_SESSION['PH2']);
        unset($_SESSION['tableID']);
        $_SESSION['onTable']=false;
    }
    

}