<?php
session_start();

// Initialisation des variables, tu peux aussi stocker en session si besoin
$name = $vorname = "";
$decalage = 0;
$yearMonth = "";

// Traitement POST-Redirect-GET pour éviter resubmit sur rafraîchissement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['Senden'])) {
        // Traitement des inputs autres que le bouton Senden

        if (isset($_POST['name'])) $name = $_POST['name'];
        if (isset($_POST['vorname'])) $vorname = $_POST['vorname'];
        if (isset($_POST['decalage'])) $decalage = (int)$_POST['decalage'];
        if (isset($_POST['startMontag'])) $yearMonth = $_POST['startMontag'];

        // Gestion navigation mois
        if (isset($_POST['-'])) {
            if ($decalage > 0) $decalage--;
        }
        if (isset($_POST['+'])) {
            if ($decalage < 13) $decalage++;
        }

        // Redirection GET vers cette page (évite resubmit)
        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    }
    // Sinon, bouton Senden cliqué : le formulaire sera envoyé vers genPdf.php, on ne fait rien ici.
}

// ********************
// DONNÉES STATIQUES (Calendrier, jours fériés, vacances...)
// ********************

$cours = [
    ["day"=>"1", "price"=>"10","name"=>"Cours du lundi"],
    ["day"=>"2", "price"=>"51","name"=>"Cours du mardi"],
    ["day"=>"2", "price"=>"12","name"=>"autre cours du mardi"],
    ["day"=>"3", "price"=>"80","name"=>"Cours du mercredi"],
    ["day"=>"4", "price"=>"55","name"=>"jeudi-ballet"]
];

// Jours fériés (statiques)
$feiertage = [
    "2025-01-01", "2025-04-18", "2025-04-21", "2025-05-01", "2025-05-29",
    "2025-06-09", "2025-06-19", "2025-10-03", "2025-11-01", "2025-12-25",
    "2025-12-26", "2026-01-01", "2026-04-03", "2026-04-06", "2026-05-01",
    "2026-05-14", "2026-05-25", "2026-06-04", "2026-10-03", "2026-11-01",
    "2026-12-25", "2026-12-26", "2027-01-01", "2027-03-26", "2027-03-29",
    "2027-05-01", "2027-05-06", "2027-05-17", "2027-05-27", "2027-10-03",
    "2027-11-01", "2027-12-25", "2027-12-26"
];

// Vacances scolaires (statiques)
$vacances = [
    ["start"=>"2025-04-14", "end"=>"2025-04-26", "year"=>"2025", "stateCode"=>"NW", "name"=>"osterferien nordrhein-westfalen 2025"],
    ["start"=>"2025-06-10", "end"=>"2025-06-10", "year"=>"2025", "stateCode"=>"NW", "name"=>"pfingstferien nordrhein-westfalen 2025"],
    ["start"=>"2025-07-14", "end"=>"2025-08-26", "year"=>"2025", "stateCode"=>"NW", "name"=>"sommerferien nordrhein-westfalen 2025"],
    ["start"=>"2025-10-13", "end"=>"2025-10-25", "year"=>"2025", "stateCode"=>"NW", "name"=>"herbstferien nordrhein-westfalen 2025"],
    ["start"=>"2025-12-22", "end"=>"2026-01-06", "year"=>"2025", "stateCode"=>"NW", "name"=>"weihnachtsferien nordrhein-westfalen 2025"]
];

// ********************
// FONCTIONS (calendrier et autres)
// ********************

function getDaysInMonth($date, $decalage, $duree, $vacances, $jferies): array {
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

    $estEnVac = 0;
    foreach ($vacances as $vacance) {
        if (strcmp($vacance["start"], $vacance["end"]) == 0 && array_key_exists($vacance["end"], $dates)) {
            $dates[$vacance["end"]]["inactive"] = 1;
        } else {
            if (array_key_exists($vacance["start"], $dates)) {
                $dates[$vacance["start"]]["inactive"] = 2;
            }
            if (array_key_exists($vacance["end"], $dates)) {
                $dates[$vacance["end"]]["inactive"] = 4;
            }
        }
    }

    foreach ($dates as $key => $date) {
        if ($date["inactive"] == 4) $estEnVac = 0;
        elseif ($date["inactive"] == 2) $estEnVac = 1;
        elseif ($estEnVac == 1) $dates[$key]["inactive"] = 3;
    }

    return $dates;
}

function getIdFromName($day): int {
    $days = ['Montag' => 0, 'Dienstag' => 1, 'Mittwoch' => 2, 'Donnerstag' => 3, 'Freitag' => 4, 'Samstag' => 5, 'Sonntag' => 6];
    return $days[$day] ?? -1;
}

// ********************
// AFFICHAGE FORMULAIRE
// ********************
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier</title>
</head>
<body>

<table>
    <form method="post">
        <tr>
            <td>Name</td>
            <td><input type="text" size="10" maxlength="150" name="name" value="<?php echo htmlspecialchars($name); ?>" required/></td>
        </tr>
        <tr>
            <td>Vorname</td>
            <td><input type="text" size="10" maxlength="150" name="vorname" value="<?php echo htmlspecialchars($vorname); ?>" required/></td>
        </tr>

        <?php
        $date = new DateTime();
        $tagen = getDaysInMonth($date, $decalage, 0, $vacances, $feiertage);
        $NbFirstDay = getIdFromName(reset($tagen)["name"]);
        ?>

        <tr>
            <td>Die Anmeldung gilt ab dem Monat</td>
            <td>
                <input type="submit" name="-" value="←">
                <input type="hidden" name="decalage" value="<?php echo $decalage ?>">
                <?php echo substr(array_key_first($tagen), 0, 7) ?>
                <input type="submit" name="+" value="→">
            </td>
        </tr>

        <tr>
            <td>Kalender</td>
            <td>
                <table border="1" cellspacing="0" cellpadding="5">
                    <tr>
                        <td>Montag</td>
                        <td>Dienstag</td>
                        <td>Mittwoch</td>
                        <td>Donnerstag</td>
                        <td>Freitag</td>
                        <td>Samstag</td>
                        <td>Sonntag</td>
                    </tr>
                    <tr>
                        <?php
                        // espaces vides avant le premier jour
                        for ($i = 0; $i < $NbFirstDay; $i++) {
                            echo "<td></td>";
                        }
                        $colCount = $NbFirstDay;
                        foreach ($tagen as $tag => $tinfo) {
                            echo "<td>";
                            if ($tinfo["inactive"] == 1) echo "<strong>";
                            elseif ($tinfo["inactive"] > 1) echo "<u>";
                            echo substr($tag, -2);
                            if ($tinfo["inactive"] == 1) echo "</strong>";
                            elseif ($tinfo["inactive"] > 1) echo "</u>";
                            echo "</td>";

                            $colCount++;
                            if ($colCount % 7 == 0) {
                                echo "</tr><tr>";
                            }
                        }
                        // Compléter la dernière ligne si besoin
                        while ($colCount % 7 != 0) {
                            echo "<td></td>";
                            $colCount++;
                        }
                        ?>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td><input type="submit" name="Senden" value="Senden" formaction="genPdf.php"/></td>
        </tr>
    </form>
</table>

</body>
</html>
