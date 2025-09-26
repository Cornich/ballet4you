<?php
// DONNÉES STATIQUES (Calendrier, jours fériés, vacances...)
/*$cours = [
    ["day"=>"1", "price"=>"10","name"=>"Cours du lundi"],
    ["day"=>"2", "price"=>"51","name"=>"Cours du mardi"],
    ["day"=>"2", "price"=>"12","name"=>"autre cours du mardi"],
    ["day"=>"3", "price"=>"80","name"=>"Cours du mercredi"],
    ["day"=>"4", "price"=>"55","name"=>"jeudi-ballet"]
];*/

/*$feiertage = [
    "2025-01-01", "2025-04-18", "2025-04-21", "2025-05-01", "2025-05-29",
    "2025-06-09", "2025-06-19", "2025-10-03", "2025-11-01", "2025-12-25",
    "2025-12-26", "2026-01-01", "2026-04-03", "2026-04-06", "2026-05-01",
    "2026-05-14", "2026-05-25", "2026-06-04", "2026-10-03", "2026-11-01",
    "2026-12-25", "2026-12-26", "2027-01-01", "2027-03-26", "2027-03-29",
    "2027-05-01", "2027-05-06", "2027-05-17", "2027-05-27", "2027-10-03",
    "2027-11-01", "2027-12-25", "2027-12-26"
];

$vacances = [
    ["start"=>"2025-04-14", "end"=>"2025-04-26", "year"=>"2025", "stateCode"=>"NW", "name"=>"osterferien nordrhein-westfalen 2025"],
    ["start"=>"2025-06-10", "end"=>"2025-06-10", "year"=>"2025", "stateCode"=>"NW", "name"=>"pfingstferien nordrhein-westfalen 2025"],
    ["start"=>"2025-07-14", "end"=>"2025-08-26", "year"=>"2025", "stateCode"=>"NW", "name"=>"sommerferien nordrhein-westfalen 2025"],
    ["start"=>"2025-10-13", "end"=>"2025-10-25", "year"=>"2025", "stateCode"=>"NW", "name"=>"herbstferien nordrhein-westfalen 2025"],
    ["start"=>"2025-12-22", "end"=>"2026-01-06", "year"=>"2025", "stateCode"=>"NW", "name"=>"weihnachtsferien nordrhein-westfalen 2025"]
];*/
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


if(isset($_SESSION['feiertage'])){
    $decalage = $_SESSION['feiertage'];    
}
else{
    $_SESSION['feiertage']=getFeiertage($year,$land_code);
    $decalage = $_SESSION['feiertage'];
}

if(isset($_SESSION['vacances'])){
    $vacances = $_SESSION['feiertage'];    
}
else{
    $_SESSION['vacances']=getVacances($year,$land_code);
    $vacances = $_SESSION['vacances'];
}

/*function getVacances($year,$land_code){
    for ($i = $year; $i <= $year+1; $i++) {
    $feiertage_url = "https://feiertage-api.de/api/?jahr=$i&nur_land=$land_code";
    $feiertage_response = file_get_contents($feiertage_url);
    foreach(json_decode($feiertage_response, true) as $nom=>$info){
        $feiertage[]=$info['datum'];    
    }
    error_log("appel API - Feirtage");
    //echo("$i");
    //$feiertage=array_merge($feiertage,json_decode($feiertage_response, true))  ;
    }
    return($feiertage);
}*/

#############################################
#Normalement c'est pareil avec les vacances##
#############################################

// 2. Vacances scolaires via ferien-api.de
function getVacances($land_code,$year){
    $ferien_url = "https://ferien-api.de/api/v1/holidays/$land_code/$year";
    $ferien_response = file_get_contents($ferien_url);
    $vacances = json_decode($ferien_response, true);
    $yearP1=$year+1;s
    $ferien_url = "https://ferien-api.de/api/v1/holidays/$land_code/$yearP1";
    $ferien_response = file_get_contents($ferien_url);
    $vacances=array_merge($vacances,json_decode($ferien_response, true));
    return($vacances);
}
function getFeiertage($year,$land_code){
    for ($i = $year; $i <= $year+1; $i++) {
    $ferien_url = "https://ferien-api.de/api/v1/holidays/$land_code/$i";
    $feiertage_response = file_get_contents($ferien_url);
    foreach(json_decode($feiertage_response, true) as $nom=>$info){
        $feiertage[]=$info['datum'];    
    }
    error_log("appel API - Feirtage");
    }
}


// FONCTIONS

function getDaysInMonth($date, $decalage, $duree, $vacances, $jferies, $conn): array {
    $start = clone $date;
    $start->modify("first day of this month");
    for ($i = 0; $i < $decalage; $i++) {
        $start->modify("first day of next month");
    }
    if ($duree == 0) {
        $end = (clone $start)->modify('last day of this month')->modify('+1 day');
    } else {
        $startMonth = $start->format("n");
        $startYear = $start->format("Y");
        if ($startMonth < 8) {
            $end = (clone $start)->modify('first day of august '.$startYear)->modify('+1 day');
        } else {
            $end = (clone $start)->modify('first day of august next year')->modify('+1 day');
        }
    }

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    $fmt = datefmt_create(
        'de-DE',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Europe/Berlin',
        IntlDateFormatter::GREGORIAN,
        'EEEE'
    );

    $dates = [];
    foreach ($period as $date) {
        $date->setTime(0, 0, 0);
        $iso = $date->format('Y-m-d');
        $dayName = $fmt->format($date);
        $dates[$iso] = ["name" => $dayName, "inactive" => 0];
    }

    foreach ($jferies as $jferie) {
        if (array_key_exists($jferie, $dates)) {
            $dates[$jferie]["inactive"] = 1;
        }
    }

    $pasdeb=1;
    foreach($vacances as $vacance){//application des vacances
        if(strcmp($vacance["start"],$vacance["end"])==0 and array_key_exists( $vacance["end"],$dates) ){//début et fin sont le même jour    
            $dates[$vacance["end"]]["inactive"]=1;
            //echo("monovac: ".$vacance["start"]." ".$vacance["end"]);
        }
        else{
            if(array_key_exists( $vacance["start"],$dates)) {
                $dates[ $vacance["start"]   ]["inactive"]=2;
                //echo("Start: ". $vacance["start"]);
                if($pasdeb==1){ $pasdeb=0;}
            }
            if(array_key_exists( $vacance["end"],$dates)) {
                $dates[ $vacance["end"] ]["inactive"]=4;
                //echo("end: ". $vacance["end"]);
                if($pasdeb==1) {$pasdeb=2;}
            }
        }
    }
    if($pasdeb==2) $estEnVac=1;
    else $estEnVac=0;

    foreach ($dates as $key => $date) {
        if ($date["inactive"] == 4) $estEnVac = 0;
        elseif ($date["inactive"] == 2) $estEnVac = 1;
        elseif ($estEnVac == 1) $dates[$key]["inactive"] = 3;
    }

    $vacancesPerso=getVacancesPerso($conn);
    /*print_r($vacancesPerso);
    echo($vacancesPerso[0]['dateDeb']);*/
    //print_r($dates);

    foreach($vacancesPerso as $vacances){//application des vacances de la BDD
        if(strcmp($vacances["dateDeb"],$vacances["dateFin"])==0 and array_key_exists( $vacances["dateFin"],$dates) ){
            $dates[$vacances["dateFin"]]["inactive"]=1;
            //secho("monovac: ".$vacances["dateDeb"]." ".$vacances["dateFin"]);
        }
        else{
            if(array_key_exists( $vacances["dateDeb"],$dates)) {
                $dates[ $vacances["dateDeb"]   ]["inactive"]=2;
                //echo("dateDeb: ". $vacances["dateDeb"]);
                if($pasdeb==1){ $pasdeb=0;}
            }
            if(array_key_exists( $vacances["dateFin"],$dates)) {
                $dates[ $vacances["dateFin"] ]["inactive"]=4;
                //echo("end: ". $vacances["dateFin"]);
                if($pasdeb==1) {$pasdeb=2;}
            }
        }
    }

    //print_r($dates);

    if($pasdeb==2)$estEnVac=1;
    else $estEnVac=0;

    foreach($dates as $key => $date ){
        if($date["inactive"]==4)$estEnVac=0;
        elseif($date["inactive"]==2) $estEnVac=1;
        elseif($estEnVac==1) {$dates[$key]["inactive"]=3;}
    }



    return $dates;
}

function getIdFromName($day): int {
    $days = ['Montag' => 0, 'Dienstag' => 1, 'Mittwoch' => 2, 'Donnerstag' => 3, 'Freitag' => 4, 'Samstag' => 5, 'Sonntag' => 6];
    return $days[$day] ?? -1;
}
