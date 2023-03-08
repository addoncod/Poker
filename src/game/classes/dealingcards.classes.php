<?php

class DealingCards{

    private $table;
    private $player;
    private $isgame;

    public function __construct($tableID,$login){
        $this->player = new Player($tableID,$login);
        $this->table = new Table($tableID);
        $this->isgame = $this->table->getValueFromTheTable('isgame');
    }

    public function manageTheDealOfCards()
    {
        if($this->table->numberofplayers>=3 && $this->isgame==0){

            $this->setDSbBbAtTheGameBeginning();

            $this->deleteCardsFromHands();

            $this->dealingCardsToHand();
        }else{
            $this->showCards();
            exit();
        }

        $this->setIsGame();
    }

    private function setDSbBbAtTheGameBeginning()
    {
        $isdealerempty = $this->table->getValueFromTheTable("dealer");
        $ishedealing = $this->table->getCertainValueChunk("dealer",1);

        if($ishedealing!="d"){
            if(empty($isdealerempty)){
                $dlogin = $this->table->getCertainValueChunk("P1",0);
                $this->table->updateTableSet("dealer","$dlogin.d");

                $sblogin = $this->table->getCertainValueChunk("P2",0);
                $this->table->updateTableSet("smallblind","$sblogin.NOTPAID");

                $bblogin = $this->table->getCertainValueChunk("P3",0);
                $this->table->updateTableSet("bigblind","$bblogin.NOTPAID");
            }
        }
    }

    private function deleteCardsFromHands()
    {
        $drawdelete = $this->table->getValueFromTheTable("drawdelete");
        $executedelete = false;
        if($drawdelete==0){
            $executedelete = true;
            $this->table->updateTableSet("drawdelete",1);
        }
        if($executedelete == true){
            for($y=1; $y<=5; $y++){
                $this->twoColumnsUpdateTableSet("P".$y."H1","P".$y."H2",NULL,NULL,'s','s');
            }
        }
    }

    private function dealingCardsToHand()
    {
        $idplayer = $this->player->whichPlayer($this->player->login);
        $until = false;

        while($until == false){
            $PH1 = $this->table->deck[rand(0,51)];
            $PH2 = $this->table->deck[rand(0,51)];

            $checkothers = true;
            for($i=1; $i<=$this->table->numberofplayers; $i++){
                $otherH1 = $this->table->getValueFromTheTable("P".$i."H1");
                $otherH2 = $this->table->getValueFromTheTable("P".$i."H2");
                if($PH1 == $otherH1 || $PH1 == $otherH2 || $PH2 == $otherH1 || $PH2 == $otherH2){
                    $checkothers=false;
                    break;
                }
            }
            if($PH1 != $PH2 && $checkothers == true){
                $until=true;
            }
        }

        $this->convertIntoLastCardsChunk($PH1,$PH2);
        $this->twoColumnsUpdateTableSet("P".$idplayer."H1","P".$idplayer."H2",$PH1,$PH2);
        $this->showCards();
    }

    private function convertIntoLastCardsChunk($PH1,$PH2)
    {
        $PH[1]=$PH1;
        $PH[2]=$PH2;
        for($x=1; $x<=2; $x++){
            $valuechunks = explode(".",$PH[$x]);
            $_SESSION["PH".$x.""]=$valuechunks[3];
        }
    }

    private function twoColumnsUpdateTableSet($column1,$column2,$value1,$value2)
    {
        $stmt = $this->table->connect()->prepare("UPDATE `table` SET $column1=?,$column2=? WHERE id=?");
        $stmt->execute(array($value1,$value2,$this->table->tableID));
    }

    private function showCards()
    {
        if(isset($_SESSION['PH1']) || isset($_SESSION['PH2'])){
            echo "<tr><th id='h1'>".$_SESSION['PH1']."</th><th id='h2'>".$_SESSION['PH2']."</th></tr>";
        }else{
            echo "<tr><th id='h1'>?</th><th id='h2'>?</th></tr>";
        }
    }

    private function setIsGame()
    {
        $associatedhandscounter=0;
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $H1 = $this->table->getValueFromTheTable("P".$x."H1");
            $H2 = $this->table->getValueFromTheTable("P".$x."H2");
            if($H1 != '' && $H2 != ''){
                $associatedhandscounter++;
            }
        }

        if($associatedhandscounter==$this->table->numberofplayers){
            $this->table->updateTableSet("isgame",1);
            $this->table->updateTableSet("drawdelete",0);
        }
    }
}