<?php
// a faire : generer un pdf affichant le nom entré dans le formulaire, 
// puis ajouter le prix du cours et le jour de la semaine
session_start();
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

    $destinataire = !empty($tuteur) ? $tuteur : $name . ' ' . $vorname;

$mpdf = new Mpdf();

// HTML avec styles CSS inline
$html = "

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
<p>Datum: </p>
</td> 
<td style='text-align: right'><p>An: $destinataire</p>
<p>$adresse</p>
</td>
</tr>
</table>
<br><br>

<h1>RECHNUNG Für den Ballettunterricht von $name $vorname:</h1>
<ul>
<li>Angemeldet in: </li>
<li>Jahr: </li>
<li><span style='font-weight: bold'>Tarif: </span></li>
<ul>
<li>Für April 2025 erlaube ich mir, die Summe von <span style='font-weight: bold'>75€</span> zu berechnen.</li>
<li><span style='font-weight: bold'>Einmalige Anmeldegebühr: 45€</span></li>
<li><span style='font-weight: bold'>April 2025: 30€ (2 unterricht @ 15€: am 08.04. und am 29.04.)</span></li>
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
$mpdf->Output("facture.pdf", "I");
    unset($_SESSION['name']);
    unset($_SESSION['vorname']);
    unset($_SESSION['geburtsdatum']);
    unset($_SESSION['erziehungsberechtigter']);
    unset($_SESSION['email']);
    unset($_SESSION['adresse']);
    exit();

}else {
    // Si on n'est pas en POST ou que le bouton Senden n'est pas cliqué, on redirige vers index.php
    header('Location: index.php');
    exit();
}


/*$html = "
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

  .bloc-text { margin-top: 20px; margin-left : 40px; margin-right: 40px;}

  h1 { font-size: 16; text-decoration: underline; }
  body { font-family: Arial, sans-serif; }
  p { font-size: 16px; }

</style>

<div class='bloc-titre'>
  <table class='titre-layout'>
    <tr>
      <td>
        <img src='img/logo-nobackground-100.png' alt='Logo' class='logo' />
      </td>
      <td style='text-align: center'>
        <div class='titre'>Ballet4you - Ballet Coaching by Maud Tolédano</div>
        <div class='sous-titre'>Aegidiusplatz 2. Aegidius-Passage. 53604 Bad Honnef (Aegidienberg)<br>maud@ballet4you.de - 0160.880.35.72<br>www.ballet4you.de</div>
      </td>
    </tr>
  </table>
</div>

<div class='bloc-text'>
<h1>AUFNAHMEVERTRAG</h1>

<p>
Name : $name<br><br>
Vorname : $vorname<br><br>
Geburtsdatum : <br><br>
Erziehungsberechtiger : <br>
(bei Teilnehmern unter 18 Jahren)<br><br>
Adresse : <br><br>
E-Mail : <br><br>
Mobiltelefon : <br><br>
Kindergarteen/Schule besucht (welche?) : <br><br>
Die Anmeldung gilt ab dem Monat : <br><br>
Der monatliche Beitrag beträgt : <br>
Sondervereinbarung? <br><br><br>
Bezahlung ab : <br><br>
<span style='font-weight: bold'>Der monatliche Beitrag wird jeweils bis zum 1. des Monats mittels SEPA-
Lastschrift von Ihrem Konto abgebucht.</span> Das Mitglied erteilt Maud Toledano
Ballet Coaching - Ballet4you, soweit keine andere Zahlungsweise vereinbart wird,
die Berechtigung, den Beitrag per SEPA-Lastschrift monatlich einzuziehen unter
der Gläubiger-ID: DE44ZZZ00002514193 und der zu zum späteren Zeitpunkt
mitgeteilten Mandatsreferenz (bis auf das Recht des gesetzlichen Widerruf von 8
Wochen)
</p>

</div>
";*/