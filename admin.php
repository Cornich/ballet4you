<?php
include 'db.php';          // Connexion à la base de données
//include 'includes/header.php';  // Entête HTML
require 'config.php';



?>

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






<?php 

function afficherVac($conn){
            $vacancesPerso=getVacancesPerso($conn);
            foreach($vacancesPerso as $vacance){
                echo("<tr><td>".$vacance['id']."</td><td>".$vacance['dateDeb']."</td><td>".$vacance['dateFin']."</td></tr>");
            }
}