<?php
// a faire : generer un pdf affichant le nom entré dans le formulaire, 
// puis ajouter le prix du cours et le jour de la semaine
session_start();
require_once 'db.php';
require_once __DIR__ . '/vendor/autoload.php'; // charge l'autoload de Composer
use Mpdf\Mpdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Senden'])) {
    // Récupérer les données du formulaire
    $name = $_POST['name'] ?? '';
    $vorname = $_POST['vorname'] ?? '';
    $birthdate = $_POST['geburtsdatum'] ?? '';
    $tuteur = $_POST['erziehungsberechtigter'] ?? '';
    $email = $_POST['email'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $moisAnnee = $_POST['moisAnnee'] ?? '';
    $cours_id = $_SESSION['cours_id'] ?? $_POST['cours_id'] ?? '';
    //$firstMonth = $_SESSION['firstMonth'] ?? null;
    //$monthDate = new DateTime($firstMonth . '-01');
    $destinataire = !empty($tuteur) ? $tuteur : $name . ' ' . $vorname;
    $selected_seances = $_SESSION['selected_seances'] ?? [];
    $result = getPriceBySelectedSeances($conn, $selected_seances);
    $priceSelectedSeances = $result['total'];
    error_log(print_r($result, true));
    $totalPrice1stMonth = $priceSelectedSeances + 45;
    $details = $result['details'];
    $sentence = implode(' | ', $details);
    $coursName = getCoursNameById($conn, $cours_id);
    $monthPrice = getMonthPriceById($conn, $cours_id);
    $dateDuJour = date("d/m/Y");

$mpdf = new Mpdf();

// HTML avec styles CSS inline
$html = "

<style>
  .bloc-titre {
    border: 6px solid #e3a0cf;
    padding: 10px;
  }

  table.titre-layout {
    width: 100%;
    border-collapse: collapse;
  }

  .titre-layout td {
    vertical-align: middle;
  }

  .logo {
    max-width: 80px;
  }

  .titre {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 15px;
  }

  .sous-titre {
    font-size: 16px;
  }

  .bloc-footer {
    border: 3px solid #e3a0cf;
  }

  .bloc-text { margin-top: 20px; margin-left : 40px; margin-right: 40px;}

  h1 { font-size: 16; text-decoration: underline; }
  body { font-family: Arial, sans-serif; }
  p { font-size: 14px; }
</style>

<div class='bloc-titre'>
  <table class='titre-layout'>
    <tr>
      <td>
        <img src='img/logo-nobackground-100.png' alt='Logo' class='logo' />
      </td>
      <td style='text-align: center'>
        <div class='titre'>Ballet4you - Ballet Coaching by Maud Tolédano</div>
        <div class='sous-titre'>Aegidiusplatz 2. Aegidius-Passage. 53604 Bad Honnef - Aegidienberg<br>www.ballet4you.de - maud@ballet4you.de - 0160.880.35.72</div>
      </td>
    </tr>
  </table>
</div>

<div class='bloc-text'>
<table style='width: 100%'>
<tr>
<td>
<p style='text-decoration: underline'>Rechnungsnummer</p>
<p>Datum: $dateDuJour</p>
</td> 
<td style='text-align: right'><p>An: $destinataire</p>
<p>$adresse</p>
</td>
</tr>
</table>
<br><br>

<h1>RECHNUNG Für den Ballettunterricht von $name $vorname:</h1>
<ul>
<li>Angemeldet in: $coursName</li>
<li><span style='font-weight: bold'>Tarif: $monthPrice €/Monat.</span></li>
<ul>
<li>Für $moisAnnee erlaube ich mir, die Summe von <span style='font-weight: bold'>$totalPrice1stMonth €</span> zu berechnen.</li>
<li><span style='font-weight: bold'>Einmalige Anmeldegebühr: 45€</span></li> 
<li><span style='font-weight: bold'>$moisAnnee : $priceSelectedSeances € ($sentence)</span></li> 
</ul>
</ul>
<br><br>Vielen Dank für die Zusammenarbeit!
<br>Mit freundlichen Grüssen. Maud Tolédano
<br><br><p style='text-align: right; font-size: 10px'>Dieser Rechnungsbetrag enthält nach § 4 Nr. 21 UStG keine USt.</p>
<br><br><br></div>
<div class='bloc-footer'> <p style='color: #e3a0cf; font-size: 10px; text-align: center;'>Maud Tolédano ● Ballet4you ● Aegidiusplatz 2. Aegidius-Passage. 53604 Bad Honnef. www.ballet4you.de ● Steuer I.D. Nummer: 52580341656</p></div>
";

// Écrire le HTML dans le PDF
$mpdf->WriteHTML($html);

// Sortie directe au navigateur (affiche le PDF)
$mpdf->Output("Rechnung $name $vorname $dateDuJour.pdf", "I");
    unset($_SESSION['name']);
    unset($_SESSION['vorname']);
    unset($_SESSION['geburtsdatum']);
    unset($_SESSION['erziehungsberechtigter']);
    unset($_SESSION['email']);
    unset($_SESSION['adresse']);
    unset($_SESSION['moisAnnee']);
    //unset($_SESSION['firstMonth']);

    exit();

}else {
    // Si on n'est pas en POST ou que le bouton Senden n'est pas cliqué, on redirige vers index.php
    header('Location: index.php');
    exit();
}