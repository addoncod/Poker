<?php

class Combination{

    private $table;
    private $player;
    
    public function __construct($tableID){
        $this->table = new Table($tableID);
        $this->player = new Player($tableID,null);
    }
    
    public function determineWinner()
    {
        for($x=1; $x<=5; $x++){

            $T[$x] = $this->table->getCertainValueChunk("T$x",0);

            $Tint[$x] = $this->replaceSignWithTheNumber($T[$x]);

            $Tsign[$x] = $this->table->getCertainValueChunk("T$x",1);

            $INTsignT[$x] = intval($Tsign[$x]);
            
        }
        
        for($x=1; $x<=$this->table->numberofplayers; $x++){

            $idPH1[$x] = $this->table->getCertainValueChunk("P".$x."H1",2);
            $idPH2[$x] = $this->table->getCertainValueChunk("P".$x."H2",2);

            $nrPH1[$x] = $this->table->getCertainValueChunk("P".$x."H1",0);
            $nrPH2[$x] = $this->table->getCertainValueChunk("P".$x."H2",0);

            $nrPH1int[$x] = $this->replaceSignWithTheNumber($nrPH1[$x]);
            $nrPH2int[$x] = $this->replaceSignWithTheNumber($nrPH2[$x]);

            $signPH1[$x] = $this->table->getCertainValueChunk("P".$x."H1",1);
            $signPH2[$x] = $this->table->getCertainValueChunk("P".$x."H2",1);

            $INTsignPH1[$x] = intval($signPH1[$x]);
            $INTsignPH2[$x] = intval($signPH2[$x]);
        }
        
        //Pair hand 
        $pairhandAndID = $this->pairHand($nrPH1,$nrPH2);

        //Table pair/three-four of kind
        $tablepairs = null;
        $threeofkindTABLE = null;
        $fourofkindTABLE = null;
        $uklad = $this->tablePairsThreeFour($T);
        if(is_array($uklad)){
            $tablepairs = $uklad;
        }else if(strlen($uklad)==3){
            $threeofkindTABLE = $uklad;
        }else if(strlen($uklad)==4){
            $fourofkindTABLE = $uklad;
        }
        
        //Straight Flush
        $this->straightFlush($Tint,$Tsign,$nrPH1int,$signPH1,$nrPH2int,$signPH2);
        
        //Four of kind
        $this->fourOfKind($fourofkindTABLE,$tablepairs,$pairhandAndID,$threeofkindTABLE,$nrPH1,$nrPH2);
        
        //Full House
        $this->fullHouse($pairhandAndID,$threeofkindTABLE,$nrPH1,$nrPH2,$tablepairs,$T);

        //Flush
        $this->flushh($INTsignT,$INTsignPH1,$INTsignPH2);

        //Straight
        $this->straight($Tint,$nrPH1int,$nrPH2int);

        //Three of kind
        $this->threeOfKind($nrPH1,$nrPH2,$tablepairs,$pairhandAndID,$T);
        
        //Double pair
        $this->doublePair($pairhandAndID,$tablepairs,$nrPH1,$nrPH2,$T);

        //Pair
        $this->pair($nrPH1,$nrPH2,$T,$pairhandAndID);

        //Highest card
        $this->highestCard($idPH1,$idPH2);
    }

    private function replaceSignWithTheNumber($sign)
    {
        switch($sign){
            case "J";
                $number=11;
                return $number;
            break;
            case "Q";
                $number=12;
                return $number;
            break;
            case "K";
                $number=13;
                return $number;
            break;
            case "A";
                $number=14;
                return $number;
            break;
            default;
                $number = intval($sign);
                return $number;
            break;
        }
    }

    private function pairHand($nrPH1,$nrPH2)
    {
        $pairhandAndID = array("0");
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            if($nrPH1[$x]==$nrPH2[$x]){
                array_push($pairhandAndID,"".$nrPH1[$x]."".$nrPH2[$x]."");
            }else{
                array_push($pairhandAndID,"0");
            }
        }
        return $pairhandAndID;
    }

    private function tablePairsThreeFour($T)
    {
        $samecardsarray = array();
        $tablepairs = array();

        for($x=1; $x<=4; $x++){
            $counter=0;
            for($i=($x+1); $i<=5; $i++)
            {
                if($T[$x]==$T[$i]){
                    if($counter==0){
                        array_push($samecardsarray,"".$T[$x]."".$T[$i]."");
                    }else{
                        array_push($samecardsarray,"".$T[$i]."");
                    }
                    $counter++;
                } 
            }

            switch($counter){
                case 1;
                    foreach($samecardsarray as $cards){
                        $pairtable=null;
                        $pairtable .= $cards;
                    }
                    array_push($tablepairs, $pairtable);
                    $pairtable=null;
                    $samecardsarray = array();
                break;

                case 2;
                    foreach($samecardsarray as $cards){
                        $threeofkindTABLE=null;
                        $threeofkindTABLE .= $cards;
                    }
                    return $threeofkindTABLE;
                    $samecardsarray = array();
                break;

                case 3;
                    foreach($samecardsarray as $cards){
                        $fourofkindTABLE=null;
                        $fourofkindTABLE .= $cards;
                    }
                    return $fourofkindTABLE;
                    $samecardsarray = array();
                break;
            }
        }
        if(count($tablepairs)>0){
            return $tablepairs;
        }
    }

    private function straightFlush($Tint,$Tsign,$nrPH1int,$signPH1,$nrPH2int,$signPH2)
    {
        $straightflushWinnersIDs = array();

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $allcards = array();
            for($i=0; $i<=5; $i++){
                if(isset($Tint[$i]) || isset($Tsign[$i])){
                    array_push($allcards,"".$Tint[$i]."".$Tsign[$i]."");
                }
            }

            array_push($allcards,"".$nrPH1int[$x]."".$signPH1[$x]."","".$nrPH2int[$x]."".$signPH2[$x]."");
            
            //Sorting algorithm
            for($i=0; $i<7; $i++){
                for($j=1; $j<8; $j++){
                    if(isset($allcards[$j-1]) && isset($allcards[$j])){
                        if(strlen($allcards[$j-1])==3 && strlen($allcards[$j])==3){
                            if(substr($allcards[$j-1],0,2)>substr($allcards[$j],0,2)){
                                $pom = $allcards[$j];
                                $allcards[$j] = $allcards[$j-1];
                                $allcards[$j-1] = $pom;
                            } 
                        }else if(strlen($allcards[$j-1])==3 && strlen($allcards[$j])==2){
                            if(substr($allcards[$j-1],0,2)>$allcards[$j][0]){
                                $pom = $allcards[$j];
                                $allcards[$j] = $allcards[$j-1];
                                $allcards[$j-1] = $pom;
                            } 
                        }else if(strlen($allcards[$j-1])==2 && strlen($allcards[$j])==3){
                            if($allcards[$j-1][0]>substr($allcards[$j],0,2)){
                                $pom = $allcards[$j];
                                $allcards[$j] = $allcards[$j-1];
                                $allcards[$j-1] = $pom;
                            } 
                        }else{
                            if($allcards[$j-1][0]>$allcards[$j][0]){
                                $pom = $allcards[$j];
                                $allcards[$j] = $allcards[$j-1];
                                $allcards[$j-1] = $pom;
                            } 
                        }
                    }
                }
            }

            //Sorted streak counter
            $streak=0;
            for($i=7; $i>0; $i--){
                if(isset($allcards[$j-1]) && isset($allcards[$j])){
                    if(strlen($allcards[$i-1])==3 && strlen($allcards[$i])==3){
                        if(((substr($allcards[$i-1],0,2)+1) == substr($allcards[$i],0,2)) 
                        && ($allcards[$i-1][2] == $allcards[$i][2])){
                            $streak++;
                            if($streak==4){
                                array_push($straightflushWinnersIDs, $x);
                                break;
                            }
                        }else{
                            $streak=0;
                        }
                    }else if(strlen($allcards[$i-1])==3 && strlen($allcards[$i])==2){
                        if(((substr($allcards[$i-1],0,2)+1) == $allcards[$i][0]) 
                        && ($allcards[$i-1][2] == $allcards[$i][1])){
                            $streak++;
                            if($streak==4){
                                array_push($straightflushWinnersIDs, $x);
                                break;
                            }
                        }else{
                            $streak=0;
                        }
                    }else if(strlen($allcards[$i-1])==2 && strlen($allcards[$i])==3){
                        if(($allcards[$i-1][0]+1 == substr($allcards[$i],0,2))
                        && ($allcards[$i-1][1] == $allcards[$i][2])){
                            $streak++;
                            if($streak==4){
                                array_push($straightflushWinnersIDs, $x);
                                break;
                            }
                        }else{
                            $streak=0;
                        }
                    }else if(strlen($allcards[$i-1])==2 && strlen($allcards[$i])==2){
                        if(($allcards[$i-1][0]+1 == $allcards[$i][0]) && ($allcards[$i-1][1] == $allcards[$i][1])){
                            $streak++;
                            if($streak==4){
                                array_push($straightflushWinnersIDs, $x);
                                break;
                            }
                        }
                    }else{
                        $streak=0;
                    }
                }
            }
        }

        $this->splitThePotAndStartNextRound($straightflushWinnersIDs,"straight flush");
    }

    private function fourOfKind($fourofkindTABLE,$tablepairs,$pairhandAndID,$threeofkindTABLE,$nrPH1,$nrPH2)
    {
        $fourofkindWinnersIDs = array();

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            if(!empty($fourofkindTABLE)){
                array_push($fourofkindWinnersIDs,$x);
                continue;
            }

            //Pairtable + Pairhand
            if((!empty($tablepairs[0]) && !empty($pairhandAndID[$x]) && $tablepairs[0][0] == $pairhandAndID[$x][0])
            || (!empty($tablepairs[1]) && !empty($pairhandAndID[$x]) && $tablepairs[1][0] == $pairhandAndID[$x][0])
            ){
                array_push($fourofkindWinnersIDs,$x);
                break;
            }

            //Three of kind + 1 card
            if(!empty($threeofkindTABLE) && 
            ($nrPH1[$x]==substr($threeofkindTABLE,0,1) || $nrPH2[$x]==substr($threeofkindTABLE,0,1))){
                array_push($fourofkindWinnersIDs,$x);
                break;
            }
        }

        $this->splitThePotAndStartNextRound($fourofkindWinnersIDs,"four of kind");
    }

    private function fullHouse($pairhandAndID,$threeofkindTABLE,$nrPH1,$nrPH2,$tablepairs,$T)
    {

        $fullhouseWinnersIDs = array();
        for($x=1; $x<=$this->table->numberofplayers; $x++){

            //Three of kind + Pairhand
            if(!empty($pairhandAndID[$x]) && !empty($threeofkindTABLE)){
                array_push($fullhouseWinnersIDs,$x);
                continue;
            }

            //Three of kind + Pair
            for($i=1; $i<=5; $i++){
                if((!empty($threeofkindTABLE) && $nrPH1[$x] != substr($threeofkindTABLE,0,1) && $nrPH1[$x]==$T[$i])
                || (!empty($threeofkindTABLE) && $nrPH2[$x] != substr($threeofkindTABLE,0,1) && $nrPH2[$x]==$T[$i])){
                    array_push($fullhouseWinnersIDs,$x);
                    continue 2;
                    break;
                }
            }
            
            //Pairtable + Three of kind
            for($i=1; $i<=5; $i++){
                if(
                (!empty($tablepairs[0]) && !empty($pairhandAndID[$x]) && $pairhandAndID[$x] != $tablepairs[0] &&
                $pairhandAndID[$x][0] == $T[$i])
                ||
                (!empty($tablepairs[1]) && !empty($pairhandAndID[$x]) && $pairhandAndID[$x] != $tablepairs[1] &&
                $pairhandAndID[$x][0] == $T[$i])){
                    array_push($fullhouseWinnersIDs,$x);
                    continue 2;
                    break;
                }
            }

            //Three of kind + Pairtable
            for($i=1; $i<=5; $i++){
                if((!empty($tablepairs[0]) && $nrPH1[$x] == $tablepairs[0][0] && $nrPH2[$x] == $T[$i])
                ||
                (!empty($tablepairs[0]) && $nrPH2[$x] == $tablepairs[0][0] && $nrPH1[$x] == $T[$i])
                ||
                ((!empty($tablepairs[1]) && $nrPH2[$x] == $tablepairs[1][0] && $nrPH1[$x] == $T[$i]))
                ||
                ((!empty($tablepairs[1]) && $nrPH1[$x] == $tablepairs[1][0] && $nrPH2[$x] == $T[$i]))
                ){
                    array_push($fullhouseWinnersIDs,$x);
                    break;
                }
            }
        }

        $this->splitThePotAndStartNextRound($fullhouseWinnersIDs,"full house");
    }

    private function flushh($INTsignT,$INTsignPH1,$INTsignPH2)
    {
        $flushWinnersIDs = array();

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $allcards = array();
            foreach($INTsignT as $t){
                array_push($allcards, $t);
            }

            array_push($allcards,$INTsignPH1[$x],$INTsignPH2[$x]);

            $signsCounter = array(null,null,null,null,null);
            foreach($allcards as $signs){
                switch ($signs){

                    case 1;
                        if($signsCounter[1]<4){
                            $signsCounter[1]+=1;
                        }else{
                            array_push($flushWinnersIDs,$x);
                            break 2;
                        }
                    break;

                    case 2;
                        if($signsCounter[2]<4){
                            $signsCounter[2]+=1;
                        }else{
                            array_push($flushWinnersIDs,$x);
                            break 2;
                        }
                    break;

                    case 3;
                        if($signsCounter[3]<4){
                            $signsCounter[3]+=1;
                        }else{
                            array_push($flushWinnersIDs,$x);
                            break 2;
                        }
                    break;

                    case 4;
                        if($signsCounter[4]<4){
                            $signsCounter[4]+=1;
                        }else{
                            array_push($flushWinnersIDs,$x);
                            break 2;
                        }
                    break;
                }
            }
        }

        $this->splitThePotAndStartNextRound($flushWinnersIDs,"flush");
    }

    private function straight($Tint,$nrPH1int,$nrPH2int)
    {
        $straightWinnersIDs = array();

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $allcards = array();
            foreach($Tint as $t){
                array_push($allcards, $t);
            }

            array_push($allcards,$nrPH1int[$x],$nrPH2int[$x]);

            //Sorting algorithm
            for($i=0; $i<7; $i++){
                for($j=1; $j<7; $j++){
                    if($allcards[$j-1]>$allcards[$j]){
                        $pom = $allcards[$j];
                        $allcards[$j] = $allcards[$j-1];
                        $allcards[$j-1] = $pom;
                    }  
                }
            }

            //Sorted streak counter
            $streak=0;
            for($i=7; $i>0; $i--){
                if(isset($allcards[$i-1]) && isset($allcards[$i])){
                    if(($allcards[$i-1]+1) == $allcards[$i]){
                        $streak++;
                        if($streak==4){
                            array_push($straightWinnersIDs, $x);
                            break;
                        }
                    }else{
                        $streak=0;
                    }
                }
            }
        }

        $this->splitThePotAndStartNextRound($straightWinnersIDs,'straight');
    }

    private function threeOfKind($nrPH1,$nrPH2,$tablepairs,$pairhandAndID,$T)
    {
        $threeofkindWinnersIDs = array();
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            //Pairtable + 1 more player card
            if(isset($tablepairs)){
                if(($nrPH1[$x] == $tablepairs[0][0] || (!empty($tablepairs[1][0]) && $nrPH1[$x] == $tablepairs[1][0]))
                || ($nrPH2[$x] == $tablepairs[0][0] || (!empty($tablepairs[1][0]) && $nrPH2[$x] == $tablepairs[1][0])))
                {
                    array_push($threeofkindWinnersIDs, $x);
                    continue;
                }
            }
            
            //Pairhand + 1 more from the table
            for($i=1; $i<=5; $i++){
                if(isset($pairhandAndID)){
                    if($pairhandAndID[$x][0]==$T[$i]){
                        array_push($threeofkindWinnersIDs, $x);
                        break;
                    }
                }
            }
        }

        $this->splitThePotAndStartNextRound($threeofkindWinnersIDs,'three of kind');
    }

    private function doublePair($pairhandAndID,$tablepairs,$nrPH1,$nrPH2,$T)
    {
        $doublepairWinnersIDs = array();

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            //Pairhand + Pairtable
            if($pairhandAndID[$x] != "0" && (!empty($tablepairs[0]) || !empty($tablepairs[1]))){
                array_push($doublepairWinnersIDs, $x);
                continue;
            }

            //Pairtable + Pair
            for($i=1; $i<=5; $i++){
                if((((!empty($tablepairs[0][0]) && $tablepairs[0][0] != $nrPH1[$x]) || 
                    (!empty($tablepairs[1][0]) && $tablepairs[1][0] != $nrPH1[$x])) && $nrPH1[$x]==$T[$i])
                    ||
                    (((!empty($tablepairs[0][0]) && $tablepairs[0][0] != $nrPH2[$x]) || 
                    (!empty($tablepairs[1][0]) && $tablepairs[1][0] != $nrPH2[$x])) && $nrPH2[$x]==$T[$i]))
                    {
                    array_push($doublepairWinnersIDs, $x);
                    continue 2;
                    break;
                }
            }
            
            //Pair + Pair
            $counter=0;
            for($i=1; $i<=5; $i++){
                if($pairhandAndID[$x][0] != $nrPH1[$x] && $nrPH1[$x]==$T[$i]){
                    $counter++;
                }
                if($pairhandAndID[$x][0] != $nrPH2[$x] && $nrPH2[$x]==$T[$i]){
                    $counter++;
                }
                if($counter==2){
                    array_push($doublepairWinnersIDs,$x);
                    break;
                }
            }
        }

        $this->splitThePotAndStartNextRound($doublepairWinnersIDs,"double pair");
    }

    private function pair($nrPH1,$nrPH2,$T,$pairhandAndID)
    {
        $pairWinnersIDs = array();
        
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            if($pairhandAndID[$x]!="0"){
                array_push($pairWinnersIDs, $x);
                continue;
            }
            for($i=1; $i<=5; $i++){
                if($nrPH1[$x]==$T[$i]){
                    array_push($pairWinnersIDs, $x);
                    break;
                }else if($nrPH2[$x]==$T[$i]){
                    array_push($pairWinnersIDs, $x);
                    break;
                }
            }
        }

        $this->splitThePotAndStartNextRound($pairWinnersIDs,"pair");
    }

    private function highestCard($idPH1,$idPH2)
    {
        $max=0;

        for($x=1; $x<=$this->table->numberofplayers; $x++){
            if($idPH1[$x]>$max){
                $max=$idPH1[$x];
                $winnerid=$x;
            }
            if($idPH2[$x]>$max){
                $max=$idPH2[$x];
                $winnerid=$x;
            }
        }

        $highestcardWinnerID = array($winnerid);
        
        $this->splitThePotAndStartNextRound($highestcardWinnerID,"highest card");
    }

    private function splitThePotAndStartNextRound($combinationWinnersIDs,$nazwaukladu)
    {
        if(count($combinationWinnersIDs)>0){

            $this->checkIfTheWinnerDidntFold($combinationWinnersIDs);

            if(count($combinationWinnersIDs)<1){
                return;
            }

            $dividepot = count($combinationWinnersIDs);
            $pot = $this->table->getValueFromTheTable("pot");

            if($dividepot>1){
                $pot/=$dividepot;
            }
            
            foreach($combinationWinnersIDs as $id){
                $winnerLogin = $this->table->getCertainValueChunk("P$id",0);
                $coins = $this->player->getPlayerValueById("coins",$winnerLogin);
                $this->player->updatePlayerSet("coins",$coins+$pot,$winnerLogin);
            }
            $this->table->updateTableSet("previousRoundSummary",'');
            
            //Table summary
            $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,'TABLE: ')");
            $T = $this->table->getTableCards();

            for($x=1; $x<=5; $x++){
                $Tcard = $this->table->getCertainValueChunksByValue($T[$x],3);
                
                if($x!=5){
                    $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,',$Tcard')");
                }else{
                    $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,',$Tcard. ')");
                }
                
            }
            
            //Player hands summary
            $login = $this->player->getPlayerLogins();
            $PH1 = $this->player->getPlayersH1();
            $PH2 = $this->player->getPlayersH2();
            
            for($x=1; $x<=$this->table->numberofplayers; $x++){
                if($x!=$this->table->numberofplayers){
                    $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,'$login[$x],$PH1[$x],$PH2[$x]/')");
                }else{
                    $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,'$login[$x],$PH1[$x],$PH2[$x].')");
                } 
            }
            
            //Winners summary
            $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,' Winners ')");
            foreach($combinationWinnersIDs as $id){
                $login = $this->table->getCertainValueChunk("P$id",0);
                $this->table->updateTableSetSql("previousRoundSummary= CONCAT(previousRoundSummary,'/- $login with the $nazwaukladu')");
            }

            //Start next game:
            
            //Set in-game status for the players and delete disconnected players
            $this->table->setIgwLoginsAndDeleteDisconnectedPlayers();
            
            //Reset table for the next game
            $this->table->resetTableForTheNextGame();

            //Set next Dealer, Smallblind and Bigblind
            $round = new Round($this->table->tableID,null);
            $round->setNextDealerSbBB();
            
            exit();
            
        }
    }
    
    private function checkIfTheWinnerDidntFold(&$combinationWinnersIDs)
    {
        $players = $this->table->getPlayers();
            
        for($x=1; $x<=$this->table->numberofplayers; $x++){
            $igstatus = $this->table->getCertainValueChunksByValue($players[$x],1);
            if($igstatus!="IGT"){
                for($i=0; $i<=$this->table->numberofplayers-1; $i++){
                    if(isset($combinationWinnersIDs[$i]) && $combinationWinnersIDs[$i]==$x){
                        unset($combinationWinnersIDs[$i]);
                        break;
                    }
                }
            }
        }

    }

}