<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ballet4you';

// Créer la BDD si elle n'existe pas
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) die("Erreur connexion : " . $conn->connect_error);

if (!$conn->query("CREATE DATABASE IF NOT EXISTS $dbname")) {
    die("Erreur création base : " . $conn->error);
}
$conn->close();

// Connexion à la BDD
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die("Connexion BDD échouée : " . $conn->connect_error);

// Création des tables
$sqlTables = "
CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    priceMonth DECIMAL(10,2) NOT NULL,
    priceUnite DECIMAL(10,2) NOT NULL
);
CREATE TABLE IF NOT EXISTS planning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    jour VARCHAR(20) NOT NULL,
    debut TIME NOT NULL,
    fin TIME NOT NULL,
    prof VARCHAR(20) NOT NULL,
    adresse varchar(40) not null,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS init_flag (
    id INT PRIMARY KEY,
    initialized BOOLEAN NOT NULL
);
CREATE TABLE IF NOT EXISTS vacances_perso(
    id INT AUTO_INCREMENT PRIMARY KEY,
    dateDeb DATE not null,
    dateFin DATE not null
)
";

if ($conn->multi_query($sqlTables)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
} else {
    die("Erreur création tables : " . $conn->error);
}

function getCoursNameById($conn, $cours_id) {
    $stmt = $conn->prepare("SELECT name FROM cours WHERE id = ?");
    $stmt->bind_param("i", $cours_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['name'] ?? '';
}


// Fonction pour insérer les cours statiques
function initCours($conn) {
    $coursList = [
        ['JAZZ für KIDS', 30.00, 10.00],
        ['HIP-HOP', 59.00, 15.00],
        ['LEISTUNG Minis', 90.00, 15.00],
        ['PRE-BALLETT 2', 46.00, 12.00],
        ['PRE-BALLETT 1', 46.00, 12.00],
        ['KIDS 1. & 2. STUFE', 56.00, 15.00],
        ['TEENS 2', 57.00, 15.00],
        ['ERWACHSENE', 59.00, 15.00],
        ['LEISTUNG Junior', 50.00, 15.00],
        ['LEISTUNG Pre-Pros', 70.00, 15.00],
        ['TEENS 1 open level', 57.00, 15.00],
    ];

    foreach ($coursList as $cours) {
        $name = $conn->real_escape_string($cours[0]);
        $priceM = $cours[1];
        $priceU = $cours[2];
        if (!$conn->query("INSERT IGNORE INTO cours (name, priceMonth, priceUnite) VALUES ('$name', $priceM, $priceU)")) {
            die("Erreur insertion cours '$name' : " . $conn->error);
        }
    }
}

// Fonction pour récupérer l'id d'un cours
function getCoursId($conn, $name) {
    $name = $conn->real_escape_string($name);
    $result = $conn->query("SELECT id FROM cours WHERE name = '$name'");
    if (!$result) {
        die("Erreur SQL dans getCoursId('$name') : " . $conn->error);
    }
    if ($row = $result->fetch_assoc()) return $row['id'];
    return null;
}

// Fonction pour insérer le planning de façon statique
function initPlanning($conn) {
    $plannings = [
    //nom , jour , heure deb, heure fin, enseignant, lieu si affinité
    ['JAZZ für KIDS', 'Montag', '17:00:00', '18:00:00', 'MARGARET', ' '],
    ['HIP-HOP', 'Montag', '18:00:00', '19:30:00', 'MARGARET', ' '],
    ['LEISTUNG Minis', 'Dienstag', '15:00:00', '16:00:00', 'MAUD', 'SPORTPARK WINDHAGEN'],
    ['PRE-BALLETT 2', 'Dienstag', '16:15:00', '17:00:00', 'NICOLE', 'SPORTPARK WINDHAGEN'],
    ['PRE-BALLETT 1', 'Dienstag', '16:00:00', '16:45:00', 'LISA', 'SPORTPARK WINDHAGEN'],
    ['KIDS 1. & 2. STUFE', 'Dienstag', '17:15:00', '18:15:00', 'LISA', ' '],
    ['TEENS 2', 'Dienstag', '18:25:00', '19:40:00', 'MAUD/LISA', ' '],
    ['ERWACHSENE', 'Dienstag', '19:45:00', '21:00:00', 'LISA', ' '],
    ['LEISTUNG Junior', 'Mittwoch', '17:00:00', '18:00:00', 'MAUD', ' '],
    ['LEISTUNG Pre-Pros', 'Mittwoch', '17:00:00', '19:00:00', 'MAUD', ' '],
    ['KIDS 1. & 2. STUFE', 'Donnerstag', '14:40:00', '15:40:00', 'MAUD', ' '],
    ['PRE-BALLETT 1', 'Donnerstag', '15:50:00', '16:35:00', 'MAUD', ' '],
    ['KIDS 1. & 2. STUFE', 'Donnerstag', '16:45:00', '17:45:00', 'MAUD', ' '],
    ['TEENS 1 open level', 'Donnerstag', '18:00:00', '19:15:00', 'CLAUDIO', ' '],
    ['ERWACHSENE', 'Donnerstag', '19:30:00', '21:00:00', 'CLAUDIO', ' '],
    ['LEISTUNG Minis', 'Freitag', '14:30:00', '15:30:00', 'MAUD', ' '],
    ['LEISTUNG Junior', 'Freitag', '15:45:00', '17:45:00', 'MAUD', ' '],
    ['LEISTUNG Pre-Pros', 'Freitag', '18:00:00', '19:30:00', 'MAUD', ' '],
    ['LEISTUNG Pre-Pros', 'Freitag', '19:30:00', '20:30:00', 'MAUD', ' '],
    ['PRE-BALLETT 1', 'Samstag', '09:45:00', '10:30:00', 'MAUD', ' '],
    ['PRE-BALLETT 2', 'Samstag', '10:45:00', '11:30:00', 'MAUD', ' '],
    ['LEISTUNG Junior', 'Samstag', '11:45:00', '13:15:00', 'MAUD', ' '],
    ['LEISTUNG Pre-Pros', 'Samstag', '14:30:00', '16:00:00', 'MAUD', ' '],
    
];

    foreach ($plannings as $p) {
        $coursId = getCoursId($conn, $p[0]);
        if (!$coursId) continue;

        $jour = $conn->real_escape_string($p[1]);
        $debut = $p[2];
        $fin = $p[3];
        $prof = $conn->real_escape_string($p[4]);
        $adresse = $conn->real_escape_string($p[5]);

        // Vérifie si ce créneau existe déjà
        $check = $conn->query("SELECT * FROM planning 
            WHERE cours_id = $coursId AND jour = '$jour' AND debut = '$debut' AND fin = '$fin'");
        if (!$check) {
            die("Erreur vérification planning : " . $conn->error);
        }
        if ($check->num_rows === 0) {
            if (!$conn->query("INSERT INTO planning (cours_id, jour, debut, fin, prof, adresse)
                          VALUES ($coursId, '$jour', '$debut', '$fin', '$prof', '$adresse')")) {
                die("Erreur insertion planning : " . $conn->error);
            }
        }
    }
}

// Fonction pour récupérer tous les cours
function getAllCours($conn) {
    $sql = "SELECT id, name FROM cours ORDER BY name";
    $result = $conn->query($sql);
    if (!$result) {
        die("Erreur SQL getAllCours : " . $conn->error);
    }
    $cours = [];
    while ($row = $result->fetch_assoc()) {
        $cours[] = $row;
    }
    return $cours;
}

// --- Gestion de l'initialisation unique ---
// Vérifie si la ligne id=1 existe dans init_flag et initialized = true
$result = $conn->query("SELECT initialized FROM init_flag WHERE id = 1");
if (!$result) {
    die("Erreur vérification init_flag : " . $conn->error);
}

if ($result->num_rows === 0) {
    // Pas de ligne => insérer ligne initialized=false pour la 1ère fois
    if (!$conn->query("INSERT INTO init_flag (id, initialized) VALUES (1, FALSE)")) {
        die("Erreur insertion init_flag : " . $conn->error);
    }
    $initialized = false;
} else {
    $row = $result->fetch_assoc();
    $initialized = (bool)$row['initialized'];
}

// Si pas initialisé, on initialise les données puis on met à jour le flag
if (!$initialized) {
    initCours($conn);
    initPlanning($conn);
    if (!$conn->query("UPDATE init_flag SET initialized = TRUE WHERE id = 1")) {
        die("Erreur mise à jour init_flag : " . $conn->error);
    }
}

function getCoursDatesInMonth($conn, $cours_id, DateTime $monthDate) {
    // Récup tous les jours de la semaine pour ce cours
    $stmt = $conn->prepare("SELECT jour FROM planning WHERE cours_id = ?");
    $stmt->bind_param("i", $cours_id); // "i" pour un entier, adapte le type si besoin
    $stmt->execute();
    $days = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (!$days) return [];

    // Map des jours allemands vers index 1 (lun) → 7 (dim)
    $map = [
        'Montag'=>1,'Dienstag'=>2,'Mittwoch'=>3,'Donnerstag'=>4,
        'Freitag'=>5,'Samstag'=>6,'Sonntag'=>7
    ];
    $targetWeekDays = array_map(fn($r)=> $map[$r['jour']], $days);

    $year = (int)$monthDate->format('Y');
    $month = (int)$monthDate->format('m');
    $result = [];

    // Itérer chaque jour du mois
    $period = new DatePeriod(
        new DateTime("$year-$month-01"),
        new DateInterval('P1D'),
        (clone new DateTime("$year-$month-01"))->modify('+1 month')
    );
    foreach ($period as $dt) {
        if (in_array((int)$dt->format('N'), $targetWeekDays)) {
            $result[] = $dt->format('Y-m-d');
        }
    }

    return $result;
}

function getMonthPriceById($conn, $cours_id) {
    $priceMonth = 0;
    // Préparer la requête SQL pour obtenir le prix unitaire du cours
    $stmt = $conn->prepare("SELECT priceMonth FROM cours WHERE id = ?");
    $stmt->bind_param("i", $cours_id);
    $stmt->execute();
    $stmt->bind_result($priceMonth);
    $stmt->fetch();
    $stmt->close();

    if (!isset($priceMonth)) {
        return 0; // Retourne 0 si le cours n'existe pas ou erreur
    }

    return round($priceMonth, 2); // Retourne un prix formaté à 2 décimales
}


function getPlanningByCoursId($conn, $cours_id) {
    $stmt = $conn->prepare("
        SELECT jour, debut, fin, prof, adresse 
        FROM planning 
        WHERE cours_id = ?
    ");
    if (!$stmt) {
        die("Erreur préparation : " . $conn->error);
    }

    $stmt->bind_param("i", $cours_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $planning = [];
    while ($row = $result->fetch_assoc()) {
        $planning[$row['jour']][] = $row;
    }
    return $planning; // ex: ['Montag' => [...], 'Dienstag' => [...]]
}



function getPriceBySelectedSeances($conn, $selectedSeances) {
    if (empty($selectedSeances)) return ['total' => 0, 'details' => []];
    $priceUnite = 0;
    $countByCours = [];        // [cours_id => count]
    $datesByCours = [];        // [cours_id => [dates]]
    $priceByCours = [];        // [cours_id => prix]

    error_log("seances: ".print_r($selectedSeances));
    foreach ($selectedSeances as $entry) {
        $parts = explode('|', $entry);
        error_log($entry);
        error_log(print_r($parts));
        if (count($parts) !== 4) continue;

        $coursId = intval($parts[0]);
        $date = $parts[1];

        // Compter les séances
        if (!isset($countByCours[$coursId])) {
            $countByCours[$coursId] = 0;
            $datesByCours[$coursId] = [];
            error_log("(!isset(countByCours[coursId]))");
        }

        $countByCours[$coursId]++;
        $datesByCours[$coursId][] = $date;
    }

    $details = [];
    $total = 0;

    error_log(print_r($countByCours));
    foreach ($countByCours as $coursId => $nbSeances) {
        // Récupérer le prix unitaire
        error_log($coursId);
        $stmt = $conn->prepare("SELECT priceUnite FROM cours WHERE id = ?");
        $stmt->bind_param("i", $coursId);
        $stmt->execute();
        $stmt->bind_result($priceUnite);
        $stmt->fetch();
        $stmt->close();

        if (!isset($priceUnite)) continue;

        $total += $nbSeances * $priceUnite;

        // Format de la phrase
        $datesList = implode(', ', $datesByCours[$coursId]);
        $sentence = "$nbSeances Unterricht @ {$priceUnite}€: am $datesList.";
    }

    return [
        'total' => round($total, 2),
        'details' => $details
    ];
}

function getVacancesPerso($conn) {
    $stmt = $conn->prepare("SELECT id, dateDeb, dateFin FROM vacances_perso");
    $stmt->execute();
    $result = $stmt->get_result();
    $vacances = [];
    while ($row = $result->fetch_assoc()) {
        $vacances[] = [
            "id" => $row['id'],
            "dateDeb" => $row['dateDeb'],
            "dateFin" => $row['dateFin']
            // Tu peux ajouter d'autres champs si besoin
        ];
    }
    return $vacances;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_vacance'])) {
    $idVac = $_POST['vacSup'] ?? null;
    if ($idVac !== null && is_numeric($idVac)) {
        $stmt = $conn->prepare("DELETE FROM vacances_perso WHERE id=?");
        $stmt->bind_param("i", $idVac);
        $stmt->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_vacance'])) {
    $dateDeb = $_POST['dateDeb'] ?? null;
    $dateFin = $_POST['dateFin'] ?? null;

    if (empty($dateDeb) || empty($dateFin)) {
        die("Les dates de début et de fin sont obligatoires.");
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateDeb) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFin)) {
        die("Format de date invalide. Utilisez le format AAAA-MM-JJ.");
    }

    $stmt = $conn->prepare("INSERT INTO vacances_perso (dateDeb, dateFin) VALUES (?, ?)");
    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("ss", $dateDeb, $dateFin);
    if (!$stmt->execute()) {
        die("Erreur lors de l'ajout des vacances : " . $stmt->error);
    }

    $stmt->close();
    //echo "Vacances ajoutées avec succès !";
}
?>