<?php

class Fold{

    private $player;
    private $table;
    private $round;
    private $playerrate;

    public function __construct($tableID,$login){
        $this->player = new Player($tableID,$login);
        $this->player->verifyPlayerTurn();
        $this->table = new Table($tableID);
        $this->round = new Round($tableID,$login);
        $this->playerrate = $this->table->getCertainValueChunk("P{$this->player->idplayer}", 3);
    }
    
    public function makeFold()
    {
        $this->round->detectBigBlindLoopCase();

        $loopcheck = $this->table->getCertainValueChunk("P{$this->player->idplayer}",4);
        if(!empty($loopcheck)){
            //InGameFalseDisconnected 
            $this->table->updateTableSet("P{$this->player->idplayer}","{$this->player->login}.IGF.CANNOTRAISE.{$this->playerrate}.LOOP");
        }else{
            //InGameFalseDisconnected 
            $this->table->updateTableSet("P{$this->player->idplayer}","{$this->player->login}.IGF.CANNOTRAISE.{$this->playerrate}");
        }

        $this->round->lastIgtCase();

        $this->round->changeTurn();
    }
}