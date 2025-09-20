<?php
    include 'db.php';          // Connexion à la base de données
    //include 'includes/header.php';  // Entête HTML
    require 'config.php';
?>

<link rel="stylesheet" href="admin.css" type="text/css">

<!DOCTYPE html>
<header>
    <title>Admin</title>
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
        <p>Les dates sont à entrer au format AAAA-MM-JJ</p>
        <p>Début: <input type="text" name="dateDeb"> </br>Fin: <input type="text" name="dateFin"></p>
        <input type="submit" name="ajouter_vacance" value="Ajouter vacance">
    </form>
        <h2>factures</h2>
        <h3>liste des factures</h3>
        <table>
            <tr><td>Numéro de facture</td><td>Cours</td><td>Début</td><td>Nom</td><td>Prénom</td><td>Prix du premier mois</td><td>Récap premier mois</td><td></td></tr>
            <?php 
                afficherFactures($conn);
            ?>
        </table>
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
  return($colors[$id-1]);
}

function afficherFactures($conn){
            $factures=getFactures($conn);
            foreach($factures as $facture){
            echo " <tr style='background-color:" . getColorFromId($facture['idCours']) . "'>";
                echo "<td>" . $facture['idFac'] . "</td>";
                echo "<td>" . $facture['name'] . "</td>";
                echo "<td>" . $facture['moisAnnee'] . "</td>";
                echo "<td>" . $facture['nom'] . "</td>";
                echo "<td>" . $facture['prenom'] . "</td>";
                echo "<td>" . $facture['totalPriceFirstMonth'] . "</td>";
                echo "<td>" . $facture['recapPremMois'] . "</td>";
                echo "<td>                    
                        <a href='genPdf.php?idFac=" . urlencode($facture['idFac']) . "&action=voirFac' target='_blank'>
                        <button type='button'>Voir</button>
                    </a></td>";
            echo "</form></tr>";
            }
}