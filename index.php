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
$moisAnnee = $_SESSION['moisAnnee'] ?? "";
$decalage = $_SESSION['decalage'] ?? 0;

$selected_seances = $_SESSION['selected_seances'] ?? [];

$cours_id = $_POST['cours_id'] ?? $_SESSION['cours_id'] ?? "";
$_SESSION['cours_id'] = $cours_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['selected_seances'])) {
                $_SESSION['selected_seances'] = $_POST['selected_seances'];
    }
    if (!isset($_POST['Senden'])) {
        if (isset($_POST['name'])) $name = $_POST['name'];
        if (isset($_POST['vorname'])) $vorname = $_POST['vorname'];
        if (isset($_POST['geburtsdatum'])) $birthdate = $_POST['geburtsdatum'];
        if (isset($_POST['erziehungsberechtigter'])) $tuteur = $_POST['erziehungsberechtigter'];
        if (isset($_POST['email'])) $email = $_POST['email'];
        if (isset($_POST['adresse'])) $adresse = $_POST['adresse'];
        if (isset($_POST['moisAnnee'])) $moisAnnee = $_POST['moisAnnee'];

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
        $_SESSION['moisAnnee'] = $moisAnnee;        

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
        $_SESSION['moisAnnee'] = $moisAnnee;        

    }
}

$date = new DateTime();
$monthDate = (clone $date)->modify("+{$decalage} months");

// Récupération des jours du mois
$tagen = getDaysInMonth($date, $decalage, 0, $vacances, $feiertage,$conn);
$firstMonth = substr(array_key_first($tagen), 0, 7);
$_SESSION['firstMonth'] = $firstMonth;
$NbFirstDay = getIdFromName(reset($tagen)["name"]);

// Récupération des dates de cours pour le calendrier
$coursDates = [];
if (!empty($cours_id)) {
    $coursDates = getCoursDatesInMonth($conn, $cours_id, $monthDate);
}
$planningDetails = [];
if (!empty($cours_id)) {
    $planningDetails = getPlanningByCoursId($conn, $cours_id);
}

$coursList = getAllCours($conn);
?>

<!DOCTYPE html>
<header>
    <title>Ballet4you: Anmeldung-Formular</title>
</header>

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
    <td>Kurs</td>
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
            <td>Anschrift</td>
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
                        //print_r($tagen);

                        for ($i = 0; $i < $NbFirstDay; $i++) echo "<td></td>";

                        $colCount = $NbFirstDay;
                        foreach ($tagen as $tag => $tinfo) {
                           
                            $classes = [];
                            $dayStr = substr($tag, -2);

                            // Si jour férié ou vacances → gris
                            if ($tinfo["inactive"] == 1 || $tinfo["inactive"] > 1) {
                                $classes[] = "ferie";
                            }

                            $jourName = $tinfo["name"]; // Ex: "Montag"
                            $infos = $planningDetails[$jourName] ?? [];
                            if (!empty($infos) && $tinfo["inactive"] == 0) {
                                $classes[] = "cours";
                            }

                            echo '<td class="'.implode(' ', $classes).'">';
                            echo "<div style='font-size:12px; text-align: center'>";
                            echo htmlspecialchars($dayStr) . '<br>';
                            foreach ($infos as $index => $info) {
                                if (!empty($infos) && $tinfo["inactive"] == 0) {
                                    /*if($decalage === 0)
                                    {*/
                                    $jourDate = htmlspecialchars($tag); // ex: "2025-07-22"
                                    $value = $cours_id . '|' . $jourDate . '|' . $info['debut'] . '|' . $info['fin'];
                                    $dateObj = new DateTime($jourDate);
                                    // Formatage du mois en toutes lettres + année
                                    $moisAnnee = $dateObj->format('F Y'); // ex: "April 2025"
                                    echo "<label style='font-size:11px; display:block;'>";
                                    echo "<input type='checkbox' class='seance-checkbox' name='selected_seances[]' value='" . $value . "' " . (in_array($value, $selected_seances) ? 'checked' : '') . ">";

                                    echo "<strong>" . htmlspecialchars($info['debut']) . " - " . htmlspecialchars($info['fin']) . "</strong>" . "<br>";
                                    echo "<strong>" . htmlspecialchars($info['adresse']) . "</strong>" . "<br>";
                                    echo "<strong>" . htmlspecialchars($info['prof']) . "</strong>" . "<br>";
                                    echo "</label>";
                                    /*}
                                    else {
                                        echo "<strong>" . htmlspecialchars($info['debut']) . " - " . htmlspecialchars($info['fin']) . "</strong>" . "<br>";
                                        echo "<strong>" . htmlspecialchars($info['adresse']) . "</strong>" . "<br>";
                                        echo "<strong>" . htmlspecialchars($info['prof']) . "</strong>" . "<br><br>";
                                    }*/
  
                                }
                            }
                            echo "</div>";
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
            <td><input type="hidden" name="senden_clicked" value="1"> 
            <input type="hidden" name="moisAnnee" value="<?php echo htmlspecialchars($moisAnnee); ?>">
            <input type="submit" name="Senden" value="Senden" formaction="genPdf.php"/></td>
        </tr>
    </form>
</table>

<script>
/*document.querySelectorAll(".seance-checkbox").forEach(checkbox => {
  checkbox.addEventListener('change', function() {
    const value = this.value;
    const checked = this.checked;

    fetch('update_seances.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ value: value, checked: checked })
    })
    .then(response => response.json())
    .then(data => {
      // Mise à jour de la liste affichée
      const list = document.getElementById('selected-seances-list');
      list.innerHTML = '';
      data.selected_seances.forEach(seance => {
        const li = document.createElement('li');
        li.textContent = seance;
        list.appendChild(li);
      });
    })
    .catch(err => console.error('Erreur AJAX:', err));
  });
});*/
</script>


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
