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
    <form>
        <h2>factures</h2>
        <h3>liste des factures</h3>
        <table>
            <tr><td>Numéro de facture</td><td>Cours</td><td>Début</td><td>Nom</td><td>Prénom</td><td>Prix du premier mois</td><td>Récap premier mois</td></tr>
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

function afficherFactures($conn){
            $factures=getFactures($conn);
            foreach($factures as $facture){
                echo("<tr>
                        <td>".$facture['idFac']."</td>
                        <td>".$facture['name']."</td>
                        <td>".$facture['moisAnnee']."</td>
                        <td>".$facture['nom']."</td>
                        <td>".$facture['prenom']."</td>
                        <td>".$facture['totalPriceFirstMonth']."</td>
                        <td>".$facture['recapPremMois']."</td>
                     </tr>");
            }
}