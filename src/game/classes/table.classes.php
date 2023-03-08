<?php

class Table extends Dbh{

    public $tableID;
    public $call;
    public $numberofplayers;
    public $deck = array(
        "2.1.1.2&#9829","2.2.2.2&#9830","2.3.3.2&#9827","2.4.4.2&#9824",
        "3.1.5.3&#9829","3.2.6.3&#9830","3.3.7.3&#9827","3.4.8.3&#9824",
        "4.1.9.4&#9829","4.2.10.4&#9830","4.3.11.4&#9827","4.4.12.4&#9824",
        "5.1.13.5&#9829","5.2.14.5&#9830","5.3.15.5&#9827","5.4.16.5&#9824",
        "6.1.17.6&#9829","6.2.18.6&#9830","6.3.19.6&#9827","6.4.20.6&#9824",
        "7.1.21.7&#9829","7.2.22.7&#9830","7.3.23.7&#9827","7.4.24.7&#9824",
        "8.1.25.8&#9829","8.2.26.8&#9830","8.3.27.8&#9827","8.4.28.4&#9824",
        "9.1.29.9&#9829","9.2.30.9&#9830","9.3.31.9&#9827","9.4.32.9&#9824",
        "10.1.33.10&#9829","10.2.34.10&#9830","10.3.35.10&#9827","10.4.36.10&#9824",
        "J.1.37.J&#9829","J.2.38.J&#9830","J.3.39.J&#9827","J.4.40.J&#9824",
        "Q.1.41.Q&#9829","Q.2.42.Q&#9830","Q.3.43.Q&#9827","Q.4.44.Q&#9824",
        "K.1.45.K&#9829","K.2.46.K&#9830","K.3.47.K&#9827","K.4.48.K&#9824",
        "A.1.49.A&#9829","A.2.50.A&#9830","A.3.51.A&#9827","A.4.52.A&#9824",
    );

    public function __construct($tableID){
        $this->tableID = $tableID;
        $this->call = $this->getValueFromTheTable('call');
        $this->numberofplayers = $this->getValueFromTheTable('numberofplayers');
    }

    public function getValueFromTheTable($value){
        $stmt = $this->connect()->prepare("SELECT `$value` FROM `table` WHERE id=?");
        $stmt->execute(array($this->tableID));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($row["$value"])){
            $value = $row["$value"];
            return $value;
        }
    }

    public function getValueFromTheTableById($value,$tableID){
        $stmt = $this->connect()->prepare("SELECT `$value` FROM `table` WHERE id=?");
        $stmt->execute(array($tableID));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($row["$value"])){
            $value = $row["$value"];
            return $value;
        }
    }

    public function updateTableSet($column,$value)
    {
        $stmt = $this->connect()->prepare("UPDATE `table` SET $column=? WHERE id=?");
        $stmt->execute(array($value,$this->tableID));
    }

    public function updateTableSetSql($columnAndValue)
    {
        $conn = $this->mysqliConnect();
        $query = "UPDATE `table` SET $columnAndValue WHERE id='{$this->tableID}'";
        mysqli_query($conn, $query);
    }


    public function getCertainValueChunk($value,$chunk)
    {
        $value = $this->getValueFromTheTable($value);
        $valuechunks = explode(".",$value);
        if(isset($valuechunks[$chunk])){
            $result = $valuechunks[$chunk];
            return $result;
        }
    }

    public function getCertainValueChunksByValue($value,$chunk)
    {
        $valuechunks = explode(".",$value);
        if(isset($valuechunks[$chunk])){
            $result = $valuechunks[$chunk];
            return $result;
        }
    }

    public function getCertainValueChunkById($value,$chunk,$tableID)
    {
        $value = $this->getValueFromTheTableById($value,$tableID);
        $valuechunks = explode(".",$value);
        $result = $valuechunks[$chunk];
        return $result;
    }

    public function getMultiplyValuesChunk($value, $howmanychunks)
    {
        $value = $this->getValueFromTheTable($value);
        $valuechunks = explode(".",$value);
        for($x=0; $x<=$howmanychunks; $x++){
            if($x==0){
                $result = $valuechunks[$x];
            }else{
                $result .= ".".$valuechunks[$x]."";
            }
        }
        return $result;
    }

    public function getPot()
    {
        $pot = $this->getValueFromTheTable("pot");
        echo $pot."🔘";   
    }

    public function getPreviousRoundSummary()
    {
        $previousroundsummary = $this->getValueFromTheTable("previousRoundSummary");

        $tableCards = $this->getCertainValueChunksByValue($previousroundsummary,0);
        $tableCardsShow = $this->getChunksForTableCardsShow($tableCards,5);

        $players = $this->getCertainValueChunksByValue($previousroundsummary,1);
        $playerchunks = $this->getSeparatePlayerChunks($players,$this->numberofplayers);
        $playersCards = $this->getChunksForPlayerCardsShow($playerchunks,2);

        $winners = $this->getCertainValueChunksByValue($previousroundsummary,2);
        $winnersChunks = $this->getSeparatePlayerChunks($winners,$this->numberofplayers);

        echo "<div id='previousTableCards'><table class='summaryCardsTable'><tr><th>$tableCardsShow[1]</th><th>$tableCardsShow[2]</th><th>$tableCardsShow[3]</th><th>$tableCardsShow[4]</th><th>$tableCardsShow[5]</th></tr></table></div>";

        echo "<div id='previousPlayerHands'>";
        for($x=0; $x<=$this->numberofplayers-1; $x++){
            echo "{$playersCards[$x][0]}<table class='summaryCardsPlayers'><tr><th>{$playersCards[$x][1]}</th><th>{$playersCards[$x][2]}</th></tr></table>";
        }
        echo "</div>";

        echo "<div id='previousWinners'>$winnersChunks[0]";
        for($x=1; $x<=count($winnersChunks)-1; $x++){
            echo "<div class='winnerSummary'>$winnersChunks[$x]</div>";
        }
        echo "</div>";
    }

    private function getChunksForTableCardsShow($value,$howmanychunks)
    {
        $valuechunks = explode(",",$value);
        for($x=0; $x<=$howmanychunks; $x++){
            if(isset($valuechunks[$x])){
                $result[$x] = $valuechunks[$x]; 
            }else{
                $result[$x] = null;
            }
        }
        return $result;
    }

    private function getSeparatePlayerChunks($value,$howmanychunks)
    {
        $valuechunks = explode("/",$value);
        for($x=0; $x<=$howmanychunks; $x++){
            if(isset($valuechunks[$x])){
                $result[$x] = $valuechunks[$x]; 
            }else{
                $result[$x] = null;
            }  
        }
        return $result;
    }

    private function getChunksForPlayerCardsShow($value,$howmanychunks)
    {
        $length = count($value);
        for($i=0; $i<=$length; $i++){
            if(isset($value[$i])){
                $valuechunks = explode(",",$value[$i]);
            }
            for($x=0; $x<=$howmanychunks; $x++){
                if(isset($valuechunks[$x])){
                    $result[$i][$x] = $valuechunks[$x]; 
                }else{
                    $result[$i][$x] = null;
                }
            }
        }
        return $result;
    }

    public function updateTablePlayersList()
    {
        //Players logins with the info
        $P = $this->getPlayers();

        for($x=1; $x<=5; $x++){
            
            if($P[$x]=='' && $x+1<=5 && $P[$x+1]!=''){
                $P[$x] = $P[$x+1];
                $this->updateTableSet("P$x","$P[$x]");
                $P[$x+1] = '';
                $this->updateTableSet("P".($x+1)."",NULL);
        
            }else if($P[$x]=='' && $x+2<=5 && $P[$x+2]!=''){
                $P[$x] = $P[$x+2];
                $this->updateTableSet("P$x","$P[$x]");
                $P[$x+2] = '';
                $this->updateTableSet("P".($x+2)."",NULL);

            }else if($P[$x]=='' && $x+3<=5 && $P[$x+3]!=''){
                $P[$x] = $P[$x+3];
                $this->updateTableSet("P$x","$P[$x]");
                $P[$x+3] = '';
                $this->updateTableSet("P".($x+3)."",NULL);           

            }else if($P[$x]=='' && $x+4<=5 && $P[$x+4]!=''){
                $P[$x] = $P[$x+4];
                $this->updateTableSet("P$x","$P[$x]");
                $P[$x+4] = ''; 
                $this->updateTableSet("P".($x+4)."",NULL);
                
            }
        }

        //Players logins
        $Plogins = $this->getPlayersLoginChunks($P);

        //Players calls
        $Pcall = $this->getPlayersCallChunks($P);

        //Player turn id
        $playerTurnId = $this->determineTurn($Plogins);

        //Player in game status
        $Pigstatus = $this->getPlayersIgstatusChunks($P,$playerTurnId);
        
        //Determine display of the seat
        $seatDisplay = $this->displayOrHideSeat($Pigstatus);
        
        //Current player seat
        $CPS = $this->currentPlayerSeat($Plogins);

        //currentPlayerSeatIncrement
        $CPSinc = $CPS;

        //playerSeats from the perspective
        $PS = $this->convertPerspective($CPSinc,$CPS);

        //Player coins
        $playerCoins = $this->getPlayersCoins($Plogins);

        $this->displayPlayers($P,$seatDisplay,$PS,$Plogins,$Pigstatus,$playerCoins,$Pcall);
        
    }
    
    private function convertPerspective(&$CPSinc,$CPS){
        $PS[1]=$CPSinc;
        for($x=2; $x<=5; $x++){
            if($CPSinc+1<=5 && $CPSinc+1!=$CPS){
                $CPSinc+=1;
                $PS[$x]=$CPSinc;
            }else if($CPS!=1){
                $CPSinc=1;
                $PS[$x]=$CPSinc;
            }else{
                return $PS;
            }
        }
        return $PS;
    }

    private function currentPlayerSeat($Plogins){
        for($x=1; $x<=5; $x++){
            if($Plogins[$x]==$_SESSION['user']){
                $currentPlayerSeatId = $x;
                return $currentPlayerSeatId;
            }
        }
    }

    public function getPlayers()
    {
        $stmt = $this->connect()->prepare("SELECT P1,P2,P3,P4,P5 FROM `table` WHERE id=?");
        $stmt->execute(array($this->tableID));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        for($x=1; $x<=5; $x++){
            if(isset($row["P$x"])){
                $P[$x] = $row["P$x"];
            }else{
                $P[$x] = null;
            }
            
        }
        return $P;
    }

    public function getPlayersCoins($Plogins)
    {
        $stmt = $this->connect()->prepare("SELECT `coins` FROM `players` WHERE `login` IN (?,?,?,?,?)");
        $stmt->execute(array($Plogins[1],$Plogins[2],$Plogins[3],$Plogins[4],$Plogins[5]));
        $x=0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $x++;
            $playerCoins[$x] = $row['coins'];
        }
        if(isset($playerCoins)){
            for($x=count($playerCoins)+1; $x<=5; $x++){
                $playerCoins[$x] = null;
            }
            return $playerCoins;
        }
    }

    public function getPlayersLoginChunks($P)
    {
        for($x=1; $x<=5; $x++){
            $valuechunks = explode(".",$P[$x]);
            $Plogin[$x] = $valuechunks[0];
        }
        return $Plogin;
    }

    private function getPlayersIgstatusChunks($P,$playerTurnId)
    {
        for($x=1; $x<=5; $x++){
            $valuechunks = explode(".",$P[$x]);
            if(!empty($valuechunks[1])){
                switch($valuechunks[1]){
                    case "IGW":
                        $Pigstatus[$x][0] = "Waiting";
                        $Pigstatus[$x][1] = 1;
                    break;
                    case "IGT":
                        if($playerTurnId==$x){
                            $Pigstatus[$x][0]="Playing";
                            $Pigstatus[$x][1]=3;
                        }else{
                            $Pigstatus[$x][0]="Playing";
                            $Pigstatus[$x][1]=2;
                        }
                    break;
                    case "IGF":
                        $Pigstatus[$x][0]="Fold";
                        $Pigstatus[$x][1]=1;
                    break;
                    case "IGFd":
                        $Pigstatus[$x][0]="Disconnected";
                        $Pigstatus[$x][1]=1;
                    break;
                }
            }else{
                $Pigstatus[$x][0] = null;
                $Pigstatus[$x][1] = null;
            }
        }
        return $Pigstatus;
    }

    private function displayOrHideSeat($Pigstatus)
    {
        for($x=1; $x<=5; $x++){
            if(is_null($Pigstatus[$x][0])){
                $seatDisplay[$x]="Hide";
            }else{
                $seatDisplay[$x]="Display";
            }
        }
        return $seatDisplay;
    }

    private function getPlayersCallChunks($P)
    {
        for($x=1; $x<=5; $x++){
            $valuechunks = explode(".",$P[$x]);
            if(isset($valuechunks[3])){
                $Pcall[$x] = $valuechunks[3]."🔘";
            }else{
                $Pcall[$x] = "null";
            }
        }
        return $Pcall;
    }
    
    private function determineTurn($Plogins)
    {
        $turn = $this->getValueFromTheTable("playerturn");
        if(!empty($turn)){
            for($x=1; $x<=5; $x++){
                if($Plogins[$x]==$turn){
                    $playerTurnId=$x;
                    return $playerTurnId;
                }
            }
        }
    }
    
    private function displayPlayers($P,$seatDisplay,$PS,$Plogins,$Pigstatus,$playerCoins,$Pcall)
    {
        echo "
        <div class='seat' id='S1{$seatDisplay[$PS[1]]}'><div class='avatar'>🍁</div><div class='L'><b>{$Plogins[$PS[1]]}</b></div><div class='igs'>{$Pigstatus[$PS[1]][0]}</div><div class='seat' id='pc1'>{$Pcall[$PS[1]]}</div><div class='shadow{$Pigstatus[$PS[1]][1]}'></div></div>
        <div class='seat' id='S2{$seatDisplay[$PS[2]]}'><div class='avatar'>💎</div><div class='L'><b>{$Plogins[$PS[2]]}</b></div><div class='igs'>{$Pigstatus[$PS[2]][0]}</div><div id='coins2'><b class='c'>{$playerCoins[$PS[2]]}$</b></div><div class='seat' id='pc2'>{$Pcall[$PS[2]]}</div><div class='shadow{$Pigstatus[$PS[2]][1]}'></div></div>
        <div class='seat' id='S3{$seatDisplay[$PS[3]]}'><div class='avatar'>🏺</div><div class='L'><b>{$Plogins[$PS[3]]}</b></div><div class='igs'>{$Pigstatus[$PS[3]][0]}</div><div id='coins3'><b class='c'>{$playerCoins[$PS[3]]}$</b></div><div class='seat' id='pc3'>{$Pcall[$PS[3]]}</div><div class='shadow{$Pigstatus[$PS[3]][1]}'></div></div>
        <div class='seat' id='S4{$seatDisplay[$PS[4]]}'><div class='avatar'>🌀</div><div class='L'><b>{$Plogins[$PS[4]]}</b></div><div class='igs'>{$Pigstatus[$PS[4]][0]}</div><div id='coins4'><b class='c'>{$playerCoins[$PS[4]]}$</b></div><div class='seat' id='pc4'>{$Pcall[$PS[4]]}</div><div class='shadow{$Pigstatus[$PS[4]][1]}'></div></div>
        <div class='seat' id='S5{$seatDisplay[$PS[5]]}'><div class='avatar'>⭐</div><div class='L'><b>{$Plogins[$PS[5]]}</b></div><div class='igs'>{$Pigstatus[$PS[5]][0]}</div><div id='coins5'><b class='c'>{$playerCoins[$PS[5]]}$</b></div><div class='seat' id='pc5'>{$Pcall[$PS[5]]}</div><div class='shadow{$Pigstatus[$PS[5]][1]}'></div></div>";
        
    }

    public function getAndShowTheTable()
    {
        $stmt = $this->connect()->prepare("SELECT T1,T2,T3,T4,T5 FROM `table` WHERE id=?");
        $stmt->execute(array($this->tableID));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        for($x=1; $x<=5; $x++){
            if(isset($row["T$x"])){
                $T[$x] = $row["T$x"];
            }else{
                $T[$x] = null;
            }
        }
        $Tshow = $this->convertIntoLastCardsChunk($T);
        echo "<tr><th>$Tshow[1]</th><th>$Tshow[2]</th><th>$Tshow[3]</th><th>$Tshow[4]</th><th>$Tshow[5]</th></tr>";
    }

    public function getTableCards()
    {
        $stmt = $this->connect()->prepare("SELECT T1,T2,T3,T4,T5 FROM `table` WHERE id=?");
        $stmt->execute(array($this->tableID));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        for($x=1; $x<=5; $x++){
            $T[$x] = $row["T$x"];
        }
        return $T;
    }

    private function convertIntoLastCardsChunk($T)
    {
        for($x=1; $x<=5; $x++){
            if($T[$x]!=null){
                $valuechunks = explode(".",$T[$x]);
                $Tshow[$x] = $valuechunks[3];
            }else{
                $Tshow[$x] = null;
            }
        }
        return $Tshow;
    }

    public function layDownTheFlop()
    {
        $until = false;
        while($until == false){
            $T1 = $this->deck[rand(0,51)];
            $T2 = $this->deck[rand(0,51)];
            $T3 = $this->deck[rand(0,51)];
            
            $checkothers = true;
            for($i=1; $i<=$this->numberofplayers; $i++){
                $otherH1 = $this->getValueFromTheTable("P".$i."H1");
                $otherH2 = $this->getValueFromTheTable("P".$i."H2");
                if($T1 == $otherH1 || $T1 == $otherH2 || $T2 == $otherH1 || $T2 == $otherH2 || $T3 == $otherH1 || $T3 == $otherH2){
                    $checkothers=false;
                    break;
                }
            }
            if($T1 != $T2 && $T1 != $T3 && $T2 != $T3 && $checkothers == true){
                $until=true;
            }
        }
        $stmt = $this->connect()->prepare("UPDATE `table` SET T1=?,T2=?,T3=? WHERE id=?");
        $stmt->execute(array($T1,$T2,$T3,$this->tableID));
    }

    public function layDownTheTurn()
    {
        $until = false;

        $T1 = $this->getValueFromTheTable("T1");
        $T2 = $this->getValueFromTheTable("T2");
        $T3 = $this->getValueFromTheTable("T3");

        while($until == false){
            $T4 = $this->deck[rand(0,51)];
            $checkothers = true;
            for($i=1; $i<=$this->numberofplayers; $i++){
                $otherH1 = $this->getValueFromTheTable("P".$i."H1");
                $otherH2 = $this->getValueFromTheTable("P".$i."H2");
                if($T4 == $otherH1 || $T4 == $otherH2){
                    $checkothers=false;
                    break;
                }
            }
            if($T4 != $T1 && $T4 !=$T2 && $T4 != $T3 && $checkothers == true){
                $until=true;
            }
        }

        $this->updateTableSet("T4",$T4);
    }

    public function layDownTheRiver()
    {
        $until = false;

        $T1 = $this->getValueFromTheTable("T1");
        $T2 = $this->getValueFromTheTable("T2");
        $T3 = $this->getValueFromTheTable("T3");
        $T4 = $this->getValueFromTheTable("T4");

        while($until == false){
            $T5 = $this->deck[rand(0,51)];
            $checkothers = true;
            for($i=1; $i<=$this->numberofplayers; $i++){
                $otherH1 = $this->getValueFromTheTable("P".$i."H1");
                $otherH2 = $this->getValueFromTheTable("P".$i."H2");
                if($T5 == $otherH1 || $T5 == $otherH2){
                    $checkothers=false;
                    break;
                }
            }
            if($T5 != $T1 && $T5 !=$T2 && $T5 != $T3 && $T5 != $T4 && $checkothers == true){
                $until=true;
            }
        }

        $this->updateTableSet("T5",$T5);
    }

    public function setIgwLoginsAndDeleteDisconnectedPlayers()
    {
        $igfdcounter;
        for($i=1; $i<=$this->numberofplayers; $i++){
            $igfdcheck = $this->getCertainValueChunk("P$i",1);

            if($igfdcheck != "IGFd"){
                $login = $this->getCertainValueChunk("P$i",0);
                $this->updateTableSet("P$i","$login.IGW.CANNOTRAISE.0");
            }else{
                $this->updateTableSet("P$i",null);
                $igfdcounter++;
            }
        }
        if(!empty($igfdcounter)){
            $this->updateTableSet("numberofplayers","".$this->numberofplayers-$igfdcounter."");
        }
    }
    
    public function resetTableForTheNextGame()
    {
        $z=0;
        $e=null;

        $stmt = $this->connect()->prepare("UPDATE `table` SET pot=?, T1=?, T2=?, T3=?, T4=?, T5=?, P1H1=?, P1H2=?, P2H1=?, P2H2=?, P3H1=?, P3H2=?, P4H1=?, P4H2=?, P5H1=?, P5H2=?,
        playerturn=?,`call`=?,round1=?,round2=?,round3=?,round4=?,isgame=?,drawdelete=? WHERE id=?");
        $stmt->execute(array($z,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$z,$z,$z,$z,$z,$z,$z,$this->tableID));
    }

    public function resetTableForTheNextGameWithDSbBB()
    {
        $z = 0;
        $e = null;

        $stmt = $this->connect()->prepare("UPDATE `table` SET pot=?,T1=?,T2=?,T3=?,T4=?,T5=?,P1H1=?,P1H2=?,P2H1=?,P2H2=?,P3H1=?,P3H2=?,P4H1=?,P4H2=?,P5H1=?,P5H2=?, 
        dealer=?,smallblind=?,bigblind=?,playerturn=?,`call`=?,round1=?,round2=?,round3=?,round4=?,isgame=?,drawdelete=? WHERE id=?");
        $stmt->execute(array($z,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$z,$z,$z,$z,$z,$z,$z,$this->tableID));
    }
    
    public function resetTableAndDeleteDisconnectedPlayers($igscounter)
    {
        $z = 0;
        $e = null;
        $nop = $this->numberofplayers-$igscounter;
        $stmt = $this->connect()->prepare("UPDATE `table` SET pot=?,numberofplayers=?,T1=?,T2=?,T3=?,T4=?,T5=?,P1H1=?,P1H2=?,P2H1=?,P2H2=?,P3H1=?,P3H2=?,P4H1=?,P4H2=?,P5H1=?,P5H2=?, 
        dealer=?,smallblind=?,bigblind=?,playerturn=?,`call`=?,round1=?,round2=?,round3=?,round4=?,isgame=?,drawdelete=? WHERE id=?");
        $stmt->execute(array($z,$nop,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$z,$z,$z,$z,$z,$z,$z,$this->tableID));
    }

    public function clearTable()
    {
        $z = 0;
        $e = null;

        $stmt = $this->connect()->prepare("UPDATE `table` SET pot=?,numberofplayers=?,T1=?,T2=?,T3=?,T4=?,T5=?,P1H1=?,P1H2=?,P2H1=?,P2H2=?,P3H1=?,P3H2=?,P4H1=?,P4H2=?,P5H1=?,P5H2=?,P1=?,P2=?,P3=?,P4=?,P5=?,
        dealer=?,smallblind=?,bigblind=?,playerturn=?,`call`=?,round1=?,round2=?,round3=?,round4=?,isgame=?,drawdelete=?,previousRoundSummary=? WHERE id=?");
        $stmt->execute(array($z,$z,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$e,$z,$z,$z,$z,$z,$z,$z,$e,$this->tableID));
    }

}