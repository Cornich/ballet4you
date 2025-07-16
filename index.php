<?php
include 'db.php';          // Connexion à la base de données
include 'includes/header.php';  // Entête HTML

session_start();
require 'config.php';

// Initialisation variables depuis session
$name = $_SESSION['name'] ?? "";
$vorname = $_SESSION['vorname'] ?? "";
$birthdate = $_SESSION['geburtsdatum'] ?? "";
$tuteur = $_SESSION['erziehungsberechtigter'] ?? "";
$email = $_SESSION['email'] ?? "";
$adresse = $_SESSION['adresse'] ?? "";

$decalage = $_SESSION['decalage'] ?? 0;

$cours_id = $_POST['cours_id'] ?? $_SESSION['cours_id'] ?? "";
$_SESSION['cours_id'] = $cours_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['Senden'])) {
        if (isset($_POST['name'])) $name = $_POST['name'];
        if (isset($_POST['vorname'])) $vorname = $_POST['vorname'];
        if (isset($_POST['geburtsdatum'])) $birthdate = $_POST['geburtsdatum'];
        if (isset($_POST['erziehungsberechtigter'])) $tuteur = $_POST['erziehungsberechtigter'];
        if (isset($_POST['email'])) $email = $_POST['email'];
        if (isset($_POST['adresse'])) $adresse = $_POST['adresse'];


        if (isset($_POST['decalage'])) $decalage = (int)$_POST['decalage'];

        if (isset($_POST['-']) && $decalage > 0) $decalage--;
        if (isset($_POST['+']) && $decalage < 13) $decalage++;

        // Stockage en session
        $_SESSION['decalage'] = $decalage;
        $_SESSION['name'] = $name;
        $_SESSION['vorname'] = $vorname;
        $_SESSION['geburtsdatum'] = $birthdate;
        $_SESSION['erziehungsberechtigter'] = $tuteur;
        $_SESSION['email'] = $email;
        $_SESSION['adresse'] = $adresse;

        

        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    } else {
        // bouton Senden cliqué, on stocke pour le genPdf.php
        $_SESSION['decalage'] = $decalage;
        $_SESSION['name'] = $name;
        $_SESSION['vorname'] = $vorname;
        $_SESSION['geburtsdatum'] = $birthdate;
        $_SESSION['erziehungsberechtigter'] = $tuteur;
        $_SESSION['email'] = $email;
        $_SESSION['adresse'] = $adresse;
    }
}

$date = new DateTime();
$monthDate = (clone $date)->modify("+{$decalage} months");

// Récupération des jours du mois
$tagen = getDaysInMonth($date, $decalage, 0, $vacances, $feiertage);
$NbFirstDay = getIdFromName(reset($tagen)["name"]);

// Récupération des dates de cours pour le calendrier
$coursDates = [];
if (!empty($cours_id)) {
    $coursDates = getCoursDatesInMonth($conn, $cours_id, $monthDate);
}

$coursList = getAllCours($conn);
?>

<!DOCTYPE html>

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
        <tr>
            <td>Geburtsdatum</td> <!-- date de naissance -->
            <td>
              <input type="text" id="geburtsdatum" name="geburtsdatum"
               value="<?php echo htmlspecialchars($birthdate); ?>"
               pattern="\d{2}/\d{2}/\d{4}"
               placeholder="jj/mm/aaaa"
               required
               onblur="checkAgeAndToggleTutor()">
            </td>
        </tr>
       <tr id="tuteur-row">
        <td>Erziehungsberechtigter</td> <!-- Tuteur -->
        <td>
            <input type="text" name="erziehungsberechtigter"
                value="<?php echo htmlspecialchars($tuteur); ?>">
        </td>
       </tr>

        <tr>
        <td>E-Mail</td>
        <td>
        <input type="email" name="email"
               value="<?php echo htmlspecialchars($email); ?>"
               required>
        </td>
        </tr>

        <tr>
    <td>Cours</td>
    <td>
        <select name="cours_id" required onchange="this.form.submit()">
            <option value="">-- Choisir un cours --</option>
            <?php foreach ($coursList as $cours): ?>
                <option value="<?php echo $cours['id']; ?>" <?php if ($cours_id == $cours['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cours['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>

        <tr>
            <td>Adresse</td>
            <td><input type="text" size="10" maxlength="150" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>" required/></td>
        </tr>
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
                        <td>Montag</td><td>Dienstag</td><td>Mittwoch</td><td>Donnerstag</td><td>Freitag</td><td>Samstag</td><td>Sonntag</td>
                    </tr>
                    <tr>
                        <?php

                        for ($i = 0; $i < $NbFirstDay; $i++) echo "<td></td>";

                        $colCount = $NbFirstDay;
                        foreach ($tagen as $tag => $tinfo) {
                           
                            $classes = [];
                            $dayStr = substr($tag, -2);

                            // Si jour férié ou vacances → gris
                            if ($tinfo["inactive"] == 1 || $tinfo["inactive"] > 1) {
                                $classes[] = "ferie";
                            }

                            // Si date correspond à un jour de cours et n’est pas férié
                            if (in_array($tag, $coursDates) && $tinfo["inactive"] == 0) {
                                $classes[] = "cours";
                            }

                            echo '<td class="'.implode(' ', $classes).'">';
                            if ($tinfo["inactive"] == 1) echo "<strong>$dayStr</strong>";
                            elseif ($tinfo["inactive"] > 1) echo "<u>$dayStr</u>";
                            else echo $dayStr;
                            echo '</td>';
                            
                            /*if ($tinfo["inactive"] == 1) echo "<strong>";
                            elseif ($tinfo["inactive"] > 1) echo "<u>";
                            echo substr($tag, -2);
                            if ($tinfo["inactive"] == 1) echo "</strong>";
                            elseif ($tinfo["inactive"] > 1) echo "</u>";
                            echo "</td>";*/

                            $colCount++;
                            if ($colCount % 7 == 0) echo "</tr><tr>";
                        }
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

<script>
// Fonction de vérification d'âge (basée sur la date au format dd/mm/yyyy)
function checkAgeAndToggleTutor() {
    const birthdateInput = document.getElementById("geburtsdatum").value;
    const tuteurRow = document.getElementById("tuteur-row");

    const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    const match = birthdateInput.match(regex);

    if (!match) {
        // Format invalide, afficher le champ tuteur par sécurité
        tuteurRow.style.display = "";
        return;
    }

    const day = parseInt(match[1], 10);
    const month = parseInt(match[2], 10) - 1; // JavaScript months start at 0
    const year = parseInt(match[3], 10);

    const birthDate = new Date(year, month, day);
    const today = new Date();

    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    if (age >= 18) {
        tuteurRow.style.display = "none";
    } else {
        tuteurRow.style.display = "";
    }
}

// Appel automatique au chargement (utile si champ pré-rempli)
window.addEventListener('DOMContentLoaded', checkAgeAndToggleTutor);
</script>

</html>
<?php
include 'includes/footer.php';  // Pied de page ?>