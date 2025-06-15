<!DOCTYPE html>
    <title>
        <meta charset="utf-8">
    </title>
    <body>
        <a href="http://www.ballet4you.de">←Hauptseite</a>
        <h1>
            Anmeldung
        </h1>
        <table>
            <tr>
                <td>
                    Name
                </td>
                <td>
                    <input type="text" size="10" maxlength="150" name="name" />         
                </td>
            </tr>
            <tr>
                <td>
                    Vorname     
                </td>
                <td>
                    <input type="text" size="10" maxlength="150" name="surname" />         
                </td>
            </tr>
            <tr>
                <td>
                    Kalender
                    
                </td>
                <td>
                    <?php

                        $land_code = 'NW'; // Nordrhein-Westfalen
                        $year = date('Y');

                        $feiertage=[];
                        // 1. Jours fériés via feiertage-api.de
                            //$feiertage_url = "https://feiertage-api.de/api/?jahr=$year&nur_land=$land_code";
                            //$feiertage_response = file_get_contents($feiertage_url);
                            //$feiertage = json_decode($feiertage_response, true);
                         $fTagen =[];
                        for ($i = $year-1; $i <= $year+2; $i++) {
                            $feiertage_url = "https://feiertage-api.de/api/?jahr=$i&nur_land=$land_code";
                            $feiertage_response = file_get_contents($feiertage_url);
                            foreach(json_decode($feiertage_response, true) as $nom=>$info){
                                $feiertage[]=$info['datum'];    
                            }
                            
                        }
                        //print_r($feiertage);


                        //echo "</br> </br> </br>";

                        // 2. Vacances scolaires via ferien-api.de
                        $ferien_url = "https://ferien-api.de/api/v1/holidays/$land_code/$year";
                        $ferien_response = file_get_contents($ferien_url);
                        $vacances = json_decode($ferien_response, true);

                        //echo "🏫 Vacances scolaires à Bonn ($year):</br>";
                            //foreach ($vacances as $vacance) {
                            //    echo "- " . $vacance['name'] . " : du " . $vacance['start'] . " au " . $vacance['end'] . "</br>";
                            //}
                        function getDaysInMonth($year, $month): array {
                            $start = new DateTime("$year-$month-01");
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
                        print_r(getDaysInMonth(2025,06));

                        
                        ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="Senden" />
                </td>
            </tr>
        </table>
    </body>
</html>