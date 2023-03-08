<?php

class Call{

    private $player;
    private $table;
    private $round;

    public function __construct($tableID,$login){
        $this->player = new Player($tableID,$login);
        $this->player->verifyPlayerTurn();
        $this->table = new Table($tableID);
        $this->round = new Round($tableID,$login);
    }

    public function makeCall()
    {
        if($this->table->call!=0)
        {
            $this->round->detectBigBlindLoopCase();

            $this->makeRightCall();

            $this->round->changeTurn();      
        }else{
            echo "You cannot call";
        }
    }
    
    private function makeRightCall()
    {
        $playerrate = $this->table->getCertainValueChunk("P{$this->player->idplayer}",3);
        $purelogin = $this->table->getMultiplyValuesChunk("P{$this->player->idplayer}",1);

        if($playerrate!=0 && $this->player->coins>=$this->table->call){
            //CallWithoutPlayerRate 
            $callwpr = $this->table->call-$playerrate;
            $this->player->updateCurrentPlayerSet("coins",$this->player->coins-$callwpr);
            $this->table->updateTableSet("P{$this->player->idplayer}","$purelogin.CANNOTRAISE.".$this->table->call."");

        }else if($playerrate==0 && $this->player->coins>=$this->table->call){
            $this->player->updateCurrentPlayerSet("coins",$this->player->coins-$this->table->call);
            $this->table->updateTableSet("P{$this->player->idplayer}","$purelogin.CANNOTRAISE.".$this->table->call."");

        }else if($this->player->coins<$this->table->call){
            $this->player->updateCurrentPlayerSet("coins",$this->player->coins-$this->player->coins);
            //playerRate+Coins
            $prpc = $playerrate + $this->player->coins;
            $this->table->updateTableSet("P{$this->player->idplayer}","$purelogin.CANNOTRAISE.$prpc");
        }
    }
}