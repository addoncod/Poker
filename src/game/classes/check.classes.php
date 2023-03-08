<?php

class Check{

    public function __construct($tableID,$login){
        $this->player = new Player($tableID,$login);
        $this->player->verifyPlayerTurn();
        $this->table = new Table($tableID);
        $this->round = new Round($tableID,$login);
    }

    public function makeCheck(){
        $playerrate = $this->table->getCertainValueChunk("P{$this->player->idplayer}",3);
        
        if($playerrate == $this->table->call){
            $this->round->changeTurn();
        }else{
            echo "You cannot check";
        }
    }
    
}