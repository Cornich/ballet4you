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
    price DECIMAL(10,2) NOT NULL
);
CREATE TABLE IF NOT EXISTS planning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    jour VARCHAR(20) NOT NULL,
    debut TIME NOT NULL,
    fin TIME NOT NULL,
    prof VARCHAR(20) NOT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS init_flag (
    id INT PRIMARY KEY,
    initialized BOOLEAN NOT NULL
);
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

// Fonction pour insérer les cours statiques
function initCours($conn) {
    $coursList = [
        ['JAZZ für KIDS', 75.00],
        ['HIP-HOP', 75.00],
        ['LEISTUNG Minis', 90.00],
        ['PRE-BALLETT 2', 70.00],
        ['PRE-BALLETT 1', 70.00],
        ['KIDS 1. & 2. STUFE', 75.00],
        ['TEENS 2', 80.00],
        ['ERWACHSENE', 80.00],
        ['LEISTUNG Junior', 90.00],
        ['LEISTUNG Pre-Pros', 100.00],
        ['TEENS 1 open level', 80.00],
    ];

    foreach ($coursList as $cours) {
        $name = $conn->real_escape_string($cours[0]);
        $price = $cours[1];
        if (!$conn->query("INSERT IGNORE INTO cours (name, price) VALUES ('$name', $price)")) {
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
        ['JAZZ für KIDS', 'Montag', '17:00:00', '18:00:00', 'MARGARET'],
        ['HIP-HOP', 'Montag', '18:00:00', '19:30:00', 'MARGARET'],
        ['LEISTUNG Minis', 'Dienstag', '15:00:00', '16:00:00', 'MAUD'],
        ['PRE-BALLETT 2', 'Dienstag', '16:15:00', '17:00:00', 'NICOLE'],
        ['PRE-BALLETT 1', 'Dienstag', '16:00:00', '16:45:00', 'LISA'],
        ['KIDS 1. & 2. STUFE', 'Dienstag', '17:15:00', '18:15:00', 'LISA'],
        ['TEENS 2', 'Dienstag', '18:25:00', '19:40:00', 'MAUD/LISA'],
        ['ERWACHSENE', 'Dienstag', '19:45:00', '21:00:00', 'LISA'],
        ['LEISTUNG Junior', 'Mittwoch', '17:00:00', '18:00:00', 'MAUD'],
        ['LEISTUNG Pre-Pros', 'Mittwoch', '17:00:00', '19:00:00', 'MAUD'],
        ['KIDS 1. & 2. STUFE', 'Donnerstag', '14:40:00', '15:40:00', 'MAUD'],
        ['PRE-BALLETT 1', 'Donnerstag', '15:50:00', '16:35:00', 'MAUD'],
        ['KIDS 1. & 2. STUFE', 'Donnerstag', '16:45:00', '17:45:00', 'MAUD'],
        ['TEENS 1 open level', 'Donnerstag', '18:00:00', '19:15:00', 'CLAUDIO'],
        ['ERWACHSENE', 'Donnerstag', '19:30:00', '21:00:00', 'CLAUDIO'],
        ['LEISTUNG Minis', 'Freitag', '14:30:00', '15:30:00', 'MAUD'],
        ['LEISTUNG Junior', 'Freitag', '15:45:00', '17:45:00', 'MAUD'],
        ['LEISTUNG Pre-Pros', 'Freitag', '18:00:00', '19:30:00', 'MAUD'],
        ['LEISTUNG Pre-Pros', 'Freitag', '19:30:00', '20:30:00', 'MAUD'],
        ['PRE-BALLETT 1', 'Samstag', '09:45:00', '10:30:00', 'MAUD'],
        ['PRE-BALLETT 2', 'Samstag', '10:45:00', '11:30:00', 'MAUD'],
        ['LEISTUNG Junior', 'Samstag', '11:45:00', '13:15:00', 'MAUD'],
        ['LEISTUNG Pre-Pros', 'Samstag', '14:30:00', '16:00:00', 'MAUD'],
    ];

    foreach ($plannings as $p) {
        $coursId = getCoursId($conn, $p[0]);
        if (!$coursId) continue;

        $jour = $conn->real_escape_string($p[1]);
        $debut = $p[2];
        $fin = $p[3];
        $prof = $conn->real_escape_string($p[4]);

        // Vérifie si ce créneau existe déjà
        $check = $conn->query("SELECT * FROM planning 
            WHERE cours_id = $coursId AND jour = '$jour' AND debut = '$debut' AND fin = '$fin'");
        if (!$check) {
            die("Erreur vérification planning : " . $conn->error);
        }
        if ($check->num_rows === 0) {
            if (!$conn->query("INSERT INTO planning (cours_id, jour, debut, fin, prof)
                          VALUES ($coursId, '$jour', '$debut', '$fin', '$prof')")) {
                die("Erreur insertion planning : " . $conn->error);
            }
        }
    }
}

// Fonction pour récupérer tous les cours
function getAllCours($conn) {
    $sql = "SELECT id, name, price FROM cours ORDER BY name";
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
?>
