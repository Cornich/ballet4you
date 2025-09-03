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
    <form method="post">
        <h2>Vacances personnalisées</h2>
        <table>
            <tr><td>id</td><td>Start</td><td>End</td></tr>
            <?php
            $vacancesPerso=getVacancesPerso($conn);
            foreach($vacancesPerso as $vacance){
                echo("<tr><td>".$vacance['id']."</td><td>".$vacance['dateDeb']."</td><td>".$vacance['dateFin']."</td></tr>");
            
            }
            ?>
        </table>
    </form>
    