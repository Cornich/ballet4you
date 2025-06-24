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
            <tr>
                <td>Name</td>
                <td><input type="text" size="10" maxlength="150" name="name" />         </td>
            </tr>
            <tr>
                <td>Vorname     </td>
                <td><input type="text" size="10" maxlength="150" name="surname" />         </td>
            </tr>
            <tr>
                <td>Kalender</td>
                <td><?php


                    $feiertage = array(
                        "Neujahrstag" => array( "datum" => "2025-01-01", "hinweis" => ""),
                        "Karfreitag" => array( "datum" => "2025-04-18", "hinweis" => ""), 
                        "Ostermontag" => array( "datum" => "2025-04-21", "hinweis" =>""), 
                        "Tag der Arbeit" => array( "datum" => "2025-05-01", "hinweis" =>""), 
                        "Christi Himmelfahrt" => array( "datum" => "2025-05-29","hinweis" =>""),
                        "Pfingstmontag" => array( "datum" => "2025-06-09", "hinweis"=> ""),
                        "Fronleichnam" => array( "datum" => "2025-06-19", "hinweis" =>""), 
                        "Tag der Deutschen Einheit" => array( "datum" => "2025-10-03", "hinweis" =>""),
                        "Allerheiligen" => array( "datum" => "2025-11-01", "hinweis" =>""),
                        "1. Weihnachtstag" => array( "datum" => "2025-12-25", "hinweis" =>""), 
                        "2. Weihnachtstag" => array( "datum" => "2025-12-26", "hinweis" =>"")
                    );



                    $vacances=array ( 
                        "0" => array ( "start" => "2025-04-14", "end" => "2025-04-26" ,"year" => "2025", "stateCode" => "NW", "name" => "osterferien nordrhein-westfalen 2025", "slug" => "osterferien nordrhein-westfalen 2025-2025-NW" ) ,
                        "1" => array ( "start" => "2025-06-10", "end" => "2025-06-10" ,"year" => "2025", "stateCode" => "NW", "name" => "pfingstferien nordrhein-westfalen 2025", "slug" => "pfingstferien nordrhein-westfalen 2025-2025-NW" ),
                        "2" => array ( "start" => "2025-07-14", "end" => "2025-08-26" ,"year" => "2025", "stateCode" => "NW", "name" => "sommerferien nordrhein-westfalen 2025", "slug" => "sommerferien nordrhein-westfalen 2025-2025-NW" ) ,
                        "3" => array ( "start" => "2025-10-13", "end" => "2025-10-25" ,"year" => "2025", "stateCode" => "NW", "name" => "herbstferien nordrhein-westfalen 2025", "slug" => "herbstferien nordrhein-westfalen 2025-2025-NW" ) ,
                        "4" => array ( "start" => "2025-12-22", "end" => "2026-01-06" ,"year" => "2025", "stateCode" => "NW", "name" => "weihnachtsferien nordrhein-westfalen 2025", "slug" => "weihnachtsferien nordrhein-westfalen 2025-2025-NW" ) ) ;
    
                        print_r($feiertage);
                        print_r($vacances);
                        $land_code = 'NW'; // Nordrhein-Westfalen
                        $year = date('Y');
                        $month=date('M');
                        $decalage=0;

                        $date = new DateTime();
                        $feiertage=[];
                        // 1. Jours fériés via feiertage-api.de
                            //$feiertage_url = "https://feiertage-api.de/api/?jahr=$year&nur_land=$land_code";
                            //$feiertage_response = file_get_contents($feiertage_url);
                            //$feiertage = json_decode($feiertage_response, true);
                         $fTagen =[];
                        for ($i = $year-1; $i <= $year+2; $i++) {
                            $feiertage_url = "https://feiertage-api.de/api/?jahr=$i&nur_land=$land_code";
                            //$feiertage_response = file_get_contents($feiertage_url);
                            foreach(json_decode($feiertage_response, true) as $nom=>$info){
                                $feiertage[]=$info['datum'];    
                            }
                            
                        }
                        //print_r($feiertage);


                        //echo "</br> </br> </br>";

                        // 2. Vacances scolaires via ferien-api.de
                        $ferien_url = "https://ferien-api.de/api/v1/holidays/$land_code/$year";
                        $ferien_response = file_get_contents($ferien_url);
                        //$vacances = json_decode($ferien_response, true);

                        //echo "🏫 Vacances scolaires à Bonn ($year):</br>";
                        //    foreach ($vacances as $vacance) {
                        //        echo "- " . $vacance['name'] . " : du " . $vacance['start'] . " au " . $vacance['end'] . "</br>";
                        //    }
                        //    echo($ferien_url);

                        function getDaysInMonth($start, $decalage): array {
                            $start->modify("+{$decalage} month")->modify("first day of this month");
                            $end = (clone $start)->modify('last day of this month')->modify('+1 day');
        
                            $interval = new DateInterval('P1D');
                            $period = new DatePeriod($start, $interval, $end);
                            $fmt = datefmt_create(
                                'de-DE',
                                IntlDateFormatter::FULL,
                                IntlDateFormatter::FULL,
                                'Europe/Berlin',
                                IntlDateFormatter::GREGORIAN,
                                'EEEE'
                            );

                                $dates = [];
                            foreach ($period as $date) {
                                $iso = $date->format('Y-m-d');
                                $dayName = $fmt->format($date);
                                $dates[$iso] = $dayName;
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
                        //echo("oui");
                        print_r($vacances); 
                            function printDates($decalage,$date):void {

                                $tagen=getDaysInMonth($date,$decalage);
                                $NbFirstDay=getIdFromName(reset($tagen));
                                echo('<table>
                                            <tr>
                                                <form method="post">
                                                <td>
                                                    <input type="submit" name ="-" value="←"></input>
                                                </td>
                                                <td><input type="hidden" name="decalage" value="'.$decalage.'">'.$decalage.'</td>
                                                <td></td>
                                                
                                                <td>
                                                    '.substr(array_key_first($tagen), 0,7).'
                                                </td>
                                                <td></td><td></td>
                                                <td>
                                                    <input type="submit" name ="+" value="→"></input>
                                                </td>
                                                </form>
                                            </tr>
                                            <tr>
                                                <td> Montag </td>

                                                <td> Dienstag </td>

                                                <td> Mittwoch </td>

                                                <td> Donnerstag </td>

                                                <td> Freitag </td>

                                                <td> Samstag </td>

                                                <td> Sonntag </td>
                                            </tr>');
                                for ($i=0; $i <$NbFirstDay ; $i++) { 
                                    echo("<td></td>");
                                }
                                foreach ($tagen as $tag => $nom) {
                                    echo("<td>".substr($tag, -2) ."</td>");
                                    if(strcmp($nom,'Sonntag')==0){
                                        echo("</tr><tr>");
                                    }
                                }
                                echo("</td>");
                            }
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                if(isset($_POST['decalage'])) {
                                    $decalage = (int)$_POST['decalage'];
                                }
                                if(isset($_POST["-"])){
                                    $decalage -=1;
                                }
                                if(isset($_POST["+"])){            
                                    $decalage +=1;
                                }
                                
                            }
                            printDates($decalage,$date);
                            ?>


                            </tr>
                        </table></td>
            </tr>
            <tr>
                <td><input type="submit" value="Senden" /></td>
            </tr>
        </table>
        
    </body>
</html>