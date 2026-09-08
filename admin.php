<?php
include 'db.php';          // Connexion à la base de données
//include 'includes/header.php';  // Entête HTML
require 'config.php';
// Identifiants de connexion
$valid_username = 'admin';
$valid_password = getMdpAdmin($conn);// password_hash('ALEX', PASSWORD_DEFAULT); // En production, utilisez un mot de passe haché
error_log($valid_password);

// Vérification des identifiants
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
        $_SERVER['PHP_AUTH_USER'] !== $valid_username ||!password_verify($_SERVER['PHP_AUTH_PW'], $valid_password)) {
    header('WWW-Authenticate: Basic realm="Zone protégée"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Accès refusé.';
    exit;
}
?>

<!DOCTYPE html>
<header>
    <title>Admin</title>
    <link rel="stylesheet" href="admin.css" type="text/css">
</header>

<body>  
    <h1>Vue administrateur</h1>
    <form method="post" formaction="db.php">
        
        <h2>Vacances personnalisées</h2>
            <h3>supprimer des vacances</h3>
            <table>
                <tr><td>id</td><td>Start</td><td>End</td></tr>
                <?php
                    afficherVac($conn);
                ?>
                <p>entrer un numéro de vacances</p><input type="text" name="vacSup"><input type="submit" name="supprimer_vacance" value="Supprimer">
            </table>
    </form>
    <h3>Ajouter des vacances</h3>
        <form method="post" formaction="db.php">
            <table>
                    <tr><td colspan='2' ><p>Les dates sont à entrer au format AAAA-MM-JJ</p></td></tr>
                    <tr><td><p>Début</td><td> <input type="text" name="dateDeb"> </td></tr>
                    <tr><td></br>Fin:</td><td> <input type="text" name="dateFin"></p></td></tr>
                    <tr><td colspan='2' ><input type="submit" name="ajouter_vacance" value="Ajouter vacance"></td></tr>
            </table>
    </form>


    <h2>Factures</h2>
    <h3>Liste des factures</h3>
    <div style="margin-bottom: 10px;">
        <form method="get" action="" style="display: inline-block; margin-right: 10px;">
            <input type="hidden" name="sort" value="cours">
            <button type="submit">Trier par type de cours</button>
        </form>
        <form method="get" action="" style="display: inline-block;">
            <input type="hidden" name="sort" value="date">
            <button type="submit">Trier par date (défaut)</button>
        </form>
    </div>
    <table>
        <tr>
            <td>Numéro de facture</td>
            <td>Cours</td>
            <td>Début</td>
            <td>Nom</td>
            <td>Prénom</td>
            <td>Mail</td>
            <td>Prix du premier mois</td>
            <td>Récap premier mois</td>
            <td></td>
        </tr>
        <?php
            $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'date';
            afficherFactures($conn, $sortBy);
        ?>
    </table>
    
    
    <h2>Cours</h2>
    <table>
        <tr><td>id</td><td>Nom</td><td>Prix mensuel</td><td>Prix individuel</tr>
        <?php afficherCours($conn);?>
        <form method="post" formaction="db.php">
            <p>Entrer un numéro de cours</p><input type="text" name="courSup"><input type="submit" name="supprimer_cours" value="Supprimer">
        </form>
        <form method="post" formaction="db.php">
            <tr>
                <td>00</td>
                <td><input type="text"   name="name"></td>
                <td><input type="text"   name="priceMonth"></td>
                <td><input type="text"   name="priceUnite"></td>
                <td><input type="submit" name="ajouter_cours" value="Ajouter"></td>
            </tr>
        </form>
    </table>

    <h2>Créneaux</h2>
    <table>
        <tr>
            <td>id_créneau</td>
            <td>id_cours</td>
            <td>jour</td>
            <td>debut</td>
            <td>fin</td>
            <td>prof</td>
            <td>adresse</td>
        </tr>
        <?php afficherPlanning($conn);?>
        <form method="post" formaction="db.php">
            <p>Entrer un id de créneau</p><input type="text" name="planSup"><input type="submit" name="supprimer_planning" value="Supprimer">
        </form>
        <form method="post" formaction="db.php">
            <tr>
                <td>00</td>
                <td><input type="text"   name="cours_id"></td>
                <td><input type="text"   name="jour"></td>
                <td><input type="text"   name="debut"></td>
                <td><input type="text"   name="fin"></td>
                <td><input type="text"   name="prof"></td>
                <td><input type="text"   name="adresse"></td>
                <td><input type="submit" name="ajouter_planning" value="Ajouter"></td>
            </tr>
        </form>
    </table>

    <h2>Modification du mot de passe</h2>
    <form method="post" formaction="db.php">
        <table>
            <tr>
                <td>Nouveau mot de passe</td>
                <td><input type="password" name="P1"></td>
            </tr>
            <tr>
                <td>Conformation du mot de passe</td>
                <td><input type="password"name="P2"></td>
            </tr>
        </table>
        <input type="submit" name="modifier_mdp" value="Changer le mot de passe">
    </form>

</body>
</html>







<?php 

function afficherVac($conn){
            $vacancesPerso=getVacancesPerso($conn);
            foreach($vacancesPerso as $vacance){
                echo("<tr><td>".$vacance['id']."</td><td>".$vacance['dateDeb']."</td><td>".$vacance['dateFin']."</td></tr>");
            }
}
function getColorFromId($id) {
    $colors=["rgb(255, 251, 139)","rgba(158, 255, 139, 1)","rgba(139, 238, 255, 1)","rgba(245, 139, 255, 1)","rgba(255, 166, 139, 1)","rgba(201, 255, 139, 1)","rgba(139, 218, 255, 1)","rgba(255, 218, 139, 1),
            rgba(192, 189, 107, 1)","rgba(105, 160, 94, 1)","rgba(87, 152, 163, 1)","rgba(164, 95, 170, 1)","rgba(143, 104, 92, 1)","rgba(107, 129, 81, 1)","rgba(86, 128, 148, 1)","rgba(158, 135, 86, 1),
            rgba(151, 148, 44, 1)","rgba(65, 158, 47, 1)","rgba(38, 139, 156, 1)","rgba(153, 47, 163, 1)","rgba(158, 92, 72, 1)","rgba(119, 167, 65, 1)","rgba(60, 115, 141, 1)","rgba(158, 127, 59, 1)"];
  return($colors[($id-1)%17]);
}

function afficherFactures($conn, $sortBy = 'date') {
    $factures = getFactures($conn, $sortBy);
    foreach ($factures as $facture) {
        echo " <tr style='background-color:" . getColorFromId($facture['idCours']) . "'>";
        echo "<td>" . $facture['idFac'] . "</td>";
        echo "<td>" . $facture['name'] . "</td>";
        echo "<td>" . $facture['moisAnnee'] . "</td>";
        echo "<td>" . $facture['nom'] . "</td>";
        echo "<td>" . $facture['prenom'] . "</td>";
        echo "<td>" . $facture['email'] . "</td>";
        echo "<td>" . $facture['totalPriceFirstMonth'] . "</td>";
        echo "<td>" . $facture['recapPremMois'] . "</td>";
        echo "<td>
                <a href='genPdf.php?idFac=" . urlencode($facture['idFac']) . "&action=voirFac' target='_blank'>
                    <button type='button'>Voir</button>
                </a>
              </td>";
        echo "</tr>";
    }
}


function afficherCours($conn){
            $cours=getAllCours($conn);
            foreach($cours as $cour){
            echo "<form method='post' formaction='db.php'>";
            echo " <tr>";
                echo "<td>" . $cour['id'] .
                         "<input type='hidden' name='id' value='".  $cour['id']."'</td>";
                echo "<td><input type='text'   name='name' value='" . $cour['name'] . "'></td>";
                echo "<td><input type='text'   name='priceMonth' value='" . $cour['priceMonth'] . "'></td>";
                echo "<td><input type='text'   name='priceUnite' value='" . $cour['priceUnite'] . "'></td>";
                echo "<td>                    
                        <button type='submit' name='modifierCours'>Modifier</button>
                    </a></td>";
            echo "</form></tr>";
            }
}
function afficherPlanning($conn){
            $plannings=getAllPlannings($conn);
            foreach($plannings as $planning){
            echo "<form method='post' formaction='db.php'>";
            echo " <tr>";
                echo "<td>" . $planning['id'].
                         "<input type='hidden' name='id' value='"      . $planning['id']."'</td>";
                echo "<td><input type='text'   name='cours_id' value='". $planning['cours_id'] . "'></td>";
                echo "<td><input type='text'   name='jour' value='"    . $planning['jour'] . "'></td>";
                echo "<td><input type='text'   name='debut' value='"   . $planning['debut'] . "'></td>";
                echo "<td><input type='text'   name='fin' value='"     . $planning['fin'] . "'></td>";
                echo "<td><input type='text'   name='prof' value='"    . $planning['prof'] . "'></td>";
                echo "<td><input type='text'   name='adresse' value='" . $planning['adresse'] . "'></td>";
                echo "<td>                    
                        <button type='submit' name='modifierPlanning'>Modifier</button>
                    </a></td>";
            echo "</form></tr>";
            }
}
