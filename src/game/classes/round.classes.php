<?php

class Round{

    private $table;
    private $player;

    public function __construct($tableID,$login){
        $this->table = new Table($tableID);
        $this->player = new Player($tableID,$login);
    }

    public function currentRoundStatus()
    {
        $isgame = $this->table->getValueFromTheTable('isgame');
        $playerturn = $this->table->getValueFromTheTable("playerturn");
      
        //Start the game
        if($isgame==1){    
            //Start the next round
            if($this->isAnyRoundInProccess()==false){
                $this->setRound();
            }
        }else{
            echo "Waiting for players...";
        }
    }

    public function changeTurn(){

        $bigblindloopcase = $this->table->getCertainValueChunk("P{$this->player->idplayer}", 4);
    
        $nptlogin = $this->nextIGTPlayerLogin($this->player->idplayer);
        
        //NextPlayerTurnID
        $nptid = $this->player->whichPlayer($nptlogin);
    
        //NextPlayerTurnLoop
        $nptloop = $this->table->getCertainValueChunk("P$nptid", 4);
    
        if($nptloop == "LOOP" || $bigblindloopcase == "LOOPbb"){
            $this->setRound();
            return; 
        }
    
        $this->table->updateTableSet("playerturn",$nptlogin);
    }
    
    private function nextIGTPlayerLogin($startPointId)
    {
        for($x=$startPointId; $x<=$this->table->numberofplayers; $x++){

            if($x==$this->table->numberofplayers){

                $x=0;
                $igfcheck = $this->table->getCertainValueChunk("P1",1);

                //InGameFalse - loop check
                $igfloopcheck = $this->table->getCertainValueChunk("P1",4);
                
                if($igfcheck == "IGT" || $igfloopcheck == "LOOP"){
                    //NextPlayerTurnLogin
                    $nptlogin = $this->table->getCertainValueChunk("P1",0);
                    return $nptlogin;
                    break;
                }
            }else{
                $igfcheck = $this->table->getCertainValueChunk("P".($x+1)."",1);
                $igfloopcheck = $this->table->getCertainValueChunk("P".($x+1)."",4);
                if($igfcheck == "IGT" || $igfloopcheck == "LOOP"){
                    $nptlogin = $this->table->getCertainValueChunk("P".($x+1)."",0);
                    return $nptlogin;     
                    break;
                }
            }  
        }
    }

    private function setRound(){
        
        if($this->isAnyRoundInProccess() == false){
            //Start the first round
            $this->table->updateTableSet("round1",1);
        
            //Set logins
            $this->setBasicLogins();
    
            //Dealer, Smallblind, Bigblind management
            $this->dealerManagement();
            $this->smallblindManagement();
            $this->bigblindManagement();
    
            //Setting Turn 
            $this->setPlayerTurnAtTheGameBeginning();
        }else{
            //Start next round
            $whichroundisinproccess = $this->whichRoundIsInProccess();
            switch($whichroundisinproccess){
                case 1;
                    $this->endPreviousRoundAndStartNext($whichroundisinproccess);
                    $this->table->layDownTheFlop();
                break;
    
                case 2;
                    $this->endPreviousRoundAndStartNext($whichroundisinproccess);
                    $this->table->layDownTheTurn();
                break;
    
                case 3;
                    $this->endPreviousRoundAndStartNext($whichroundisinproccess);
                    $this->table->layDownTheRiver();
                break;
    
                case 4;
                    $combination = new Combination($this->table->tableID);
                    $combination->determineWinner();
                break;
            }
        }
    }

    private function isAnyRoundInProccess(){
        $isanyroundinproccess=false;

            for($i=1; $i<=4; $i++){
                $roundinproccess = $this->table->getValueFromTheTable("round$i");
                if($roundinproccess == 1){
                    $isanyroundinproccess = true;
                    break;
                }
            }
            return $isanyroundinproccess;
    }

    private function whichRoundIsInProccess(){
        $whichroundisinproccess;

            for($i=1; $i<=4; $i++){
                $roundinproccess = $this->table->getValueFromTheTable("round$i");
                if($roundinproccess == 1){
                    $whichroundisinproccess = $i;
                    break;
                }
            }
            return $whichroundisinproccess;
    }
    
    private function setPlayerTurnAtTheGameBeginning()
    {
        $bigblindlogin = $this->table->getCertainValueChunk("bigblind", 0);
        for($x=1; $x<=$this->table->numberofplayers; $x++){
        
            //LoginToCompare
            $logintc = $this->table->getCertainValueChunk("P$x", 0);
            
            //Check for player turn state
            $ptempty = $this->table->getValueFromTheTable('playerturn');

            //Set player turn
            if($logintc==$bigblindlogin && $ptempty == null){
                if($x==$this->table->numberofplayers){
                    //Player next to big blind
                    $pntb = $this->table->getCertainValueChunk("P1",0);
                    $this->table->updateTableSet("playerturn",$pntb);
                    break;
                }else{
                    $pntb = $this->table->getCertainValueChunk("P".($x+1)."",0);
                    $this->table->updateTableSet("playerturn",$pntb);
                    break;
                }
            }
        }
    }

    private function setBasicLogins()
    {
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $infotologin = $this->table->getCertainValueChunk("P$x", 0);
            $coinscheck = $this->player->getPlayerValueById("coins",$infotologin);
            
            if($coinscheck<200){
                $this->player->updatePlayerSet("coins",200,$infotologin); 
            }
            //Set login
            $this->table->updateTableSet("P$x","$infotologin.IGT.CANRAISE.0");
        }
    }

    private function dealerManagement()
    {
        //dealerLoginWithoutD
        $dealerLoginWd = $this->table->getCertainValueChunk("dealer",0);
        $this->table->updateTableSet("dealer",$dealerLoginWd);
    }

    private function smallblindManagement()
    {
        $smallblindpaidstatus = $this->table->getCertainValueChunk("smallblind",1);
        if($smallblindpaidstatus == "NOTPAID")
        {
            $smallblindlogin = $this->table->getCertainValueChunk("smallblind",0);
            $sbid = $this->player->whichPlayer($smallblindlogin);

            $coins = $this->player->getPlayerValueById("coins",$smallblindlogin);
            $this->player->updatePlayerSet("coins",$coins-150,$smallblindlogin);
            $this->table->updateTableSet("smallblind","$smallblindlogin.PAID");
            $this->table->updateTableSet("P$sbid","$smallblindlogin.IGT.CANRAISE.150");
        }
    }

    private function bigblindManagement()
    {
        $bigblindpaidstatus = $this->table->getCertainValueChunk("bigblind",1);
            if($bigblindpaidstatus == "NOTPAID"){
                $bigblindlogin = $this->table->getCertainValueChunk("bigblind",0);
                $bbid = $this->player->whichPlayer($bigblindlogin);

                $coins = $this->player->getPlayerValueById("coins",$bigblindlogin);
                $this->player->updatePlayerSet("coins",$coins-200,$bigblindlogin);
                $this->table->updateTableSet("bigblind","$bigblindlogin.PAID");
                $this->table->updateTableSet("P$bbid","$bigblindlogin.IGT.CANRAISE.200.LOOPbb");
                $this->table->updateTableSet("`call`",200);
            }
    }

    private function endPreviousRoundAndStartNext($whichroundisinproccess)
    {
        $this->loginManagement();
        
        //Setting loop for the player who is next to dealer
        $whoisdealer = $this->table->getValueFromTheTable("dealer");
        $dealerid = $this->player->whichPlayer($whoisdealer);
        
        //NextPlayerRoundLoopLogin
        $nprllogin = $this->nextIGTPlayerLogin($dealerid);

        $this->table->updateTableSet("`call`",0);
        $this->table->updateTableSet("round$whichroundisinproccess",0);
        $this->table->updateTableSet("round".($whichroundisinproccess+1)."",1);

        //Set loop and turn
        $this->setNextRoundLoopAndTurn($nprllogin);
    }

    private function loginManagement()
    {
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            //Get player calls
            $playerrate[$x] = $this->table->getCertainValueChunk("P$x",3);
            $playersrates=0;
            $playersrates += $playerrate[$x];

            $playeringamestatus = $this->table->getCertainValueChunk("P$x",1);
            
            //Login + In game status
            $loginigs = $this->table->getMultiplyValuesChunk("P$x",1);

            if($playeringamestatus == "IGT"){
                $this->table->updateTableSet("P$x","$loginigs.CANRAISE.0");
            }else{
                $this->table->updateTableSet("P$x","$loginigs.CANNOTRAISE.0");
            }
        }

        $pot = $this->table->getValueFromTheTable("pot");
        $this->table->updateTableSet("pot","".($pot+$playersrates)."");
    }

    private function setNextRoundLoopAndTurn($nprllogin)
    {
        $nprlid = $this->player->whichPlayer($nprllogin);
        $nprlfl = $this->table->getMultiplyValuesChunk("P$nprlid",3);
        $this->table->updateTableSet("P$nprlid","$nprlfl.LOOP");
        $this->table->updateTableSet("playerturn",$nprllogin);
    }

    public function setPlayerRateAndTableCall($raiseorcoins)
    {
        $playerrate = $this->table->getCertainValueChunk("P{$this->player->idplayer}",3);
        if($playerrate!=0){
            //raiseWithoutPlayerRate
            $raisewpr = $raiseorcoins-$playerrate;
            $playerrate += $raisewpr;
        }else{
            $raisewpr = $raiseorcoins;
            $playerrate = $raiseorcoins;
        }

        $coins = $this->player->getPlayerValue("coins");
        $this->player->updateCurrentPlayerSet("coins",$coins-$raisewpr);

        //Set call as raise value
        $this->table->updateTableSet("`call`",$raiseorcoins);
        
        return $playerrate;
    }

    public function setLoopForTheRaiser($playerrate)
    {
        $previousloopid;
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $loop = $this->table->getCertainValueChunk("P$x",4);
            if(!empty($loop)){
                $previousloopid=$x;
                break;
            }
        }
        //Delete loop for previous player
        $previouslooplogin = $this->table->getMultiplyValuesChunk("P$previousloopid", 3);
        $this->table->updateTableSet("P$previousloopid","$previouslooplogin");

        //Set loop for the raiser
        $firstchunksoflogin = $this->table->getMultiplyValuesChunk("P{$this->player->idplayer}",1);
        $this->table->updateTableSet("P{$this->player->idplayer}","$firstchunksoflogin.CANNOTRAISE.$playerrate.LOOP");
    }

    public function detectBigBlindLoopCase()
    {
        $bigblindloop = $this->table->getCertainValueChunk("P{$this->player->idplayer}",4);
        if($bigblindloop == "LOOPbb"){
            echo "You can only check or raise";
            exit();
        }
    }

    public function lastIgtCase()
    {
        $counter=0;
        //PotentialWinnerId
        $pwid;

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $igtcheck = $this->table->getCertainValueChunk("P$x",1);
            if($igtcheck == "IGT"){
                $counter++;
                if($counter==1)
                {
                    $pwid=$x;
                }
            }
        }

        if($counter==1){
            //Winner login
            $wl = $this->table->getCertainValueChunk("P$pwid",0);

            //Assign pot to the winner
            $pot = $this->table->getValueFromTheTable("pot");
            $coins = $this->player->getPlayerValueById("coins",$wl);
            $this->player->updatePlayerSet("coins",$coins+$pot,$wl);

            //Set next game
            $this->table->setIgwLoginsAndDeleteDisconnectedPlayers();
            $this->table->resetTableForTheNextGame();
            $this->setNextDealerSbBB();
            
            exit();
        }
    }

    public function setNextDealerSbBB()
    {
        //Determine dealer, smallblind and bigblind logins
        $dealerlogin = $this->table->getValueFromTheTable("dealer");
        $sblogin = $this->table->getCertainValueChunk("smallblind",0);
        $bblogin = $this->table->getCertainValueChunk("bigblind",0);

        //Determine dealer, smallblind and bigblind ids
        $dealerid = $this->player->whichPlayer($dealerlogin);
        $sbid = $this->player->whichPlayer($sblogin);
        $bbid = $this->player->whichPlayer($bblogin);

        //Setting id+1
        $this->setDealerSbBb($dealerid,"dealer");
        $this->setDealerSbBb($sbid,"smallblind");
        $this->setDealerSbBb($bbid,"bigblind");
    }

    private function setDealerSbBb($id,$who)
    {
        //Setting next dealer, smallblind and bigblind
        if($id!=$this->table->numberofplayers){
            $nextidlogin = $this->table->getCertainValueChunk("P".($id+1)."",0);
        }else{
            $nextidlogin = $this->table->getCertainValueChunk("P1",0);
        }

        if($who=="bigblind" || $who=="smallblind"){
            $this->table->updateTableSet($who,"$nextidlogin.NOTPAID");
        }else{
            $this->table->updateTableSet($who,$nextidlogin);
        }
    }
    

    
}