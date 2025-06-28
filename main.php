<!DOCTYPE html>
    <header>
        <title>
            B4Y: Réservation
        </title>
        <meta charset="utf-8">
    </header>   
    <body>
        <a href="http://www.ballet4you.de">←Hauptseite</a>
        <h1>
            Anmeldung
        </h1>
        <table>
            <form method="post">
                <?php
                    #Si on ne teste pas directement l'api, préférer les dates en statique pour éviter de trop la harceler
                    $cours=array(
                                "0"=>array("day"=>"1", "price"=>"10","name"=>"Cours du lundi"),
                                "1"=>array("day"=>"2", "price"=>"51","name"=>"Cours du mardi"),
                                "2"=>array("day"=>"2", "price"=>"12","name"=>"autre cours du mardi"),
                                "3"=>array("day"=>"3", "price"=>"80","name"=>"Cours du mercredi"),
                                "4"=>array("day"=>"4", "price"=>"55","name"=>"jeudi-ballet"));
                
                    
                    $name="";
                    $vorname="";
                    $feiertage =array ( 
                        "0" => "2025-01-01" ,
                        "1" => "2025-04-18" ,
                        "2" => "2025-04-21" ,
                        "3" => "2025-05-01" ,
                        "4" => "2025-05-29" ,
                        "5" => "2025-06-09" ,
                        "6" => "2025-06-19" ,
                        "7" => "2025-10-03" ,
                        "8" => "2025-11-01" ,
                        "9" => "2025-12-25" ,
                        "10" => "2025-12-26", 
                        "11" => "2026-01-01", 
                        "12" => "2026-04-03", 
                        "13" => "2026-04-06", 
                        "14" => "2026-05-01", 
                        "15" => "2026-05-14", 
                        "16" => "2026-05-25", 
                        "17" => "2026-06-04", 
                        "18" => "2026-10-03", 
                        "19" => "2026-11-01", 
                        "20" => "2026-12-25", 
                        "21" => "2026-12-26", 
                        "22" => "2027-01-01", 
                        "23" => "2027-03-26", 
                        "24" => "2027-03-29", 
                        "25" => "2027-05-01", 
                        "26" => "2027-05-06", 
                        "27" => "2027-05-17", 
                        "28" => "2027-05-27", 
                        "29" => "2027-10-03", 
                        "30" => "2027-11-01", 
                        "31" => "2027-12-25", 
                        "32" => "2027-12-26" ) ;



                    $vacances=array ( 
                        "0" => array ( "start" => "2025-04-14", "end" => "2025-04-26" ,"year" => "2025", "stateCode" => "NW", "name" => "osterferien nordrhein-westfalen 2025", "slug" => "osterferien nordrhein-westfalen 2025-2025-NW" ) ,
                        "1" => array ( "start" => "2025-06-10", "end" => "2025-06-10" ,"year" => "2025", "stateCode" => "NW", "name" => "pfingstferien nordrhein-westfalen 2025", "slug" => "pfingstferien nordrhein-westfalen 2025-2025-NW" ),
                        "2" => array ( "start" => "2025-07-14", "end" => "2025-08-26" ,"year" => "2025", "stateCode" => "NW", "name" => "sommerferien nordrhein-westfalen 2025", "slug" => "sommerferien nordrhein-westfalen 2025-2025-NW" ) ,
                        "3" => array ( "start" => "2025-10-13", "end" => "2025-10-25" ,"year" => "2025", "stateCode" => "NW", "name" => "herbstferien nordrhein-westfalen 2025", "slug" => "herbstferien nordrhein-westfalen 2025-2025-NW" ) ,
                        "4" => array ( "start" => "2025-12-22", "end" => "2026-01-06" ,"year" => "2025", "stateCode" => "NW", "name" => "weihnachtsferien nordrhein-westfalen 2025", "slug" => "weihnachtsferien nordrhein-westfalen 2025-2025-NW" ) 
                    ) ;



                        //print_r($feiertage);
                    //print_r($vacances);
                    #INFOS POUR DIRE À L'API QUOI RÉCUPÉRER
                    $land_code = 'NW'; // Nordrhein-Westfalen
                    $year = date('Y');
                    $month=date('M');
                    $yearMonth=date('Y-M');
                    $decalage=0;

                    $date = new DateTime();
                    
                    // 1. Jours fériés via feiertage-api.de
                        //$feiertage_url = "https://feiertage-api.de/api/?jahr=$year&nur_land=$land_code";
                        //$feiertage_response = file_get_contents($feiertage_url);
                        //$feiertage = json_decode($feiertage_response, true);

                    ########################################################
                    #####Decommanter pour appel dynamique à l'api###########
                    ########################################################
                    /*
                    $feiertage=[];
                    for ($i = $year; $i <= $year+2; $i++) {
                        $feiertage_url = "https://feiertage-api.de/api/?jahr=$i&nur_land=$land_code";
                        $feiertage_response = file_get_contents($feiertage_url);
                        foreach(json_decode($feiertage_response, true) as $nom=>$info){
                            $feiertage[]=$info['datum'];    
                        }
                        //echo("$i");
                        //$feiertage=array_merge($feiertage,json_decode($feiertage_response, true))  ;
                    }*/
                    echo($decalage."feiertage:");
                    print_r($feiertage);
                    echo("</br>");

                    #############################################
                    #Normalement c'est pareil avec les vacances##
                    #############################################
                    //echo "</br> </br> </br>";

                    // 2. Vacances scolaires via ferien-api.de
                    //$ferien_url = "https://ferien-api.de/api/v1/holidays/$land_code/$year";
                    //$ferien_response = file_get_contents($ferien_url);
                    //$vacances = json_decode($ferien_response, true);

                    //echo "🏫 Vacances scolaires à Bonn ($year):</br>";
                    //    foreach ($vacances as $vacance) {
                    //        echo "- " . $vacance['name'] . " : du " . $vacance['start'] . " au " . $vacance['end'] . "</br>";
                    //    }
                    //    echo($ferien_url);

                    #fonction utilisée aussi bien pour afficher le calendrier (un mois) que pour avoir tous les jours de l'année (utile pour calculer combien de jeudis on aura→nb cours→prix)
                    function getDaysInMonth($date, $decalage,$duree,$vacances,$jferies): array {//duree: variable qui dit si on demande un mois ou jusqu'à la fin de l'année (fin juillet)
                        $start=clone $date;
                        $start->modify("first day of this month");
                        for($i=0; $i<$decalage;$i++){
                            $start->modify("first day of next month");
                           // echo("Décallage:".$start->format('Y-M-d')."</br>");
                        }
                        if($duree==0) $end = (clone $start)->modify('last day of this month')->modify('+1 day');
                        else{
                            $startMonth = $start->format("n"); // Récupère le mois actuel (1 pour janvier, 2 pour février, etc.)
                            $startYear = $start->format("Y"); // Récupère l'année actuelle
                            //echo('start: '.$startMonth."/".$startYear."(".$start->format('Y-M-d')."||".$decalage.")");
                            if($startMonth<8){
                                $end = (clone $start)->modify('first day of august'.$startYear)->modify('+1 day');
                            }
                            else{
                                $end = (clone $start)->modify('first day of august next year')->modify('+1 day');
                            }
                        }
    
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($start, $interval, $end);//liste de tous les jours entre le début et la fin
                        $fmt = datefmt_create(
                            'de-DE',
                            IntlDateFormatter::FULL,
                            IntlDateFormatter::NONE,
                            'Europe/Berlin',
                            IntlDateFormatter::GREGORIAN,
                            'EEEE'
                        );

                        $dates = [];
                        foreach ($period as $date) {//enregistrement de ces jours ainsi que leur nom(lundi mardi etc)

                            $date->setTime(0, 0, 0);
                            $iso = $date->format('Y-m-d');
                            $dayName = $fmt->format($date);
                            //echo($iso." ".$dayName."</br>");
                            $dates[$iso] = array("name"=>$dayName,"inactive"=>0);
                        }

                        ##########################
                        #########INFO#############
                        /#Innactive: {0: jour travaillé, 1: jour férié, 2: début des vacances, 3: jour de vacances lambda, 4: Fin de vacances}
                        ###########################
                        foreach($jferies as $jferie){//appplication des jours fériés
                            if(array_key_exists( $jferie,$dates))
                            $dates[$jferie]["inactive"]=1;
                        }
                                                
                        $pasdeb=1;
                        foreach($vacances as $vacance){//application des vacances
                            if(strcmp($vacance["start"],$vacance["end"])==0 and array_key_exists( $vacance["end"],$dates) ){
                                $dates[$vacance["end"]]["inactive"]=1;
                                echo("monovac: ".$vacance["start"]." ".$vacance["end"]);
                            }
                            else{
                                if(array_key_exists( $vacance["start"],$dates)) {
                                    $dates[ $vacance["start"]   ]["inactive"]=2;
                                    echo("Start: ". $vacance["start"]);
                                    if($pasdeb==1){ $pasdeb=0;}
                                }
                                if(array_key_exists( $vacance["end"],$dates)) {
                                    $dates[ $vacance["end"] ]["inactive"]=4;
                                    echo("end: ". $vacance["end"]);
                                    if($pasdeb==1) {$pasdeb=2;}
                                }
                            }
                        }

                        if($pasdeb==2)$estEnVac=1;
                        else $estEnVac=0;

                        foreach($dates as $key => $date ){
                            if($date["inactive"]==4)$estEnVac=0;
                            elseif($date["inactive"]==2) $estEnVac=1;
                            elseif($estEnVac==1) {$dates[$key]["inactive"]=3;}
                        }

                        
                        return $dates;
                    } 
                    function getIdFromName($day): int{
                        $days = ['Montag' => 0, 'Dienstag' => 1, 'Mittwoch' => 2, 'Donnerstag' => 3, 'Freitag' => 4, 'Samstag' => 5, 'Sonntag' => 6];
                        return $days[$day] ?? -1;
                    }
                    //print_r(getDaysInMonth(2025,06));
                    //$tagen=getDaysInMonth($date,$decalage);
                    //$NbFirstDay=getIdFromName(reset($tagen));
                    //print_r($vacances); 
                ?>
                <tr>
                    <?php function rempForm($name,$vorname,$yearMonth):void{ ?>
                    <td>Name</td>
                    <td><input type="text" size="10" maxlength="150" name="name" value="<?php echo($name); ?>" required/></td>
                </tr>
                <tr>
                    <td>Vorname     </td>
                    <td><input type="text" size="10" maxlength="150" name="vorname" value="<?php echo($vorname); ?>" required/></td>
                </tr>
                    <?php } ?>


                        <?php
                        function printDates($decalage,$date,$vacances,$feiertage):void {
                            $tagen=getDaysInMonth($date,$decalage,0,$vacances,$feiertage);
                            print_r($tagen);
                            $NbFirstDay=getIdFromName(reset($tagen)["name"]);?>
                            <tr>
                                <td>Die Anmeldung gilt ab dem Monat </td>
                                <td>
                                    <input type="submit" name ="-" value="←"></input>
                                    <input type="hidden" name="decalage" value="<?php echo($decalage)?>" ><?php //echo($decalage)?>                       
                                    <?php echo(substr(array_key_first($tagen), 0,7))?>
                                    <input type="submit" name ="+" value="→"></input>
                                </td>
                            </tr>
                            <tr>
                                <td>Kalender</td>
                                <td>
                                    <table>
                                        <tr>
                                            <td> Montag </td>

                                            <td> Dienstag </td>

                                            <td> Mittwoch </td>

                                            <td> Donnerstag </td>

                                            <td> Freitag </td>

                                            <td> Samstag </td>

                                            <td> Sonntag </td>
                                        </tr>
                            <?php
                            for ($i=0; $i <$NbFirstDay ; $i++) { 
                                echo("<td></td>");
                            }
                            foreach ($tagen as $tag => $tinfo) {//impression des jours avec un formatage particulier selon si les jours sont travailéls, fériés ou vacanciers
                                echo("<td>");
                                if($tinfo["inactive"]==1) echo("<strong>");
                                elseif($tinfo["inactive"]>1) echo("<u>");
                                echo(substr($tag, -2));
                                if($tinfo["inactive"]==1) echo("</strong>");
                                elseif($tinfo["inactive"]>1) echo("</u>");
                                echo("</td>");
                                if(strcmp($tinfo["name"],'Sonntag')==0){
                                    echo("</tr><tr>");
                                }
                            }
                            echo("</td>");
                        }                        


                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {//obtention des données pour pouvoir les garder entre chaque chargement de page (qui a lieu à chaque fois qu'on met à jour le calendrier via les flèches)
                            if(isset($_POST['name'])) {
                                $name=$_POST['name'];
                            }                               
                            if(isset($_POST['vorname'])) {
                                $vorname=$_POST['vorname'];
                            }
                            if(isset($_POST["startMontag"])){
                                $yearMonth=$_POST["startMontag"];
                            }

                            if(isset($_POST['decalage'])) {
                                $decalage= (int)$_POST['decalage'];
                            }                    
                            if(isset($_POST["-"])){
                                if($decalage>0) $decalage -=1;
                            }
                            if(isset($_POST["+"])){            
                                if($decalage<13) $decalage +=1;
                            }
                            
                        }
                        //appel des fonction pour les rééxécuter à chaque chargement 
                        rempForm($name,$vorname,$yearMonth); // pour garder ce que l'utilisateur a dit
                        printDates($decalage,$date,$vacances,$feiertage); // pour mettre à jour le calendrier
                        
                        //print_r(getDaysInMonth($date, $decalage,1,$vacances,$feiertage));

                        ?>


                    </tr>
                    </table></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Senden" /></td>
                </tr>
            </table>
        </form>
    </body>
</html>