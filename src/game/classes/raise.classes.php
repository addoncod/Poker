<?php

class Raise{

    private $player;
    private $raise;
    private $table;
    private $round;
    private $canraise;

    public function __construct($tableID,$login,$raise){
        $this->player = new Player($tableID,$login);
        $this->player->verifyPlayerTurn();
        $this->raise = $raise;
        $this->table = new Table($tableID);
        $this->round = new Round($tableID,$login);
        $this->canraise = $this->table->getCertainValueChunk("P{$this->player->idplayer}",2);
    }

    public function makeRaise()
    {
        if($this->canraise == "CANRAISE" && $this->raise > 0 && $this->player->coins >= $this->raise && $this->raise>=($this->table->call*2)){
            $this->makeProperRaise($this->raise);
        }else{
            echo "You cannot raise";
        }
    }

    public function betAllIn()
    {
        if($this->canraise == "CANRAISE" && $this->player->coins > $this->table->call){
            $this->makeProperRaise($this->player->coins);
        }else{
            echo "You cannot bet all-in";
        }
    }

    private function makeProperRaise($raiseorcoins)
    {
        $playerrate = $this->round->setPlayerRateAndTableCall($raiseorcoins);
        $this->round->setLoopForTheRaiser($playerrate);
        $this->round->changeTurn();
    }

}