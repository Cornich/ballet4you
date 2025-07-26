<?php
session_start();

// On lit les données JSON envoyées en POST
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['selected_seances'])) {
    $_SESSION['selected_seances'] = [];
}

$value = $input['value'] ?? '';
$checked = $input['checked'] ?? false;

if ($checked) {
    // Ajouter la séance si elle n'est pas déjà dans la session
    if (!in_array($value, $_SESSION['selected_seances'])) {
        $_SESSION['selected_seances'][] = $value;
    }
} else {
    // Supprimer la séance décochée
    $_SESSION['selected_seances'] = array_filter($_SESSION['selected_seances'], function($v) use ($value) {
        return $v !== $value;
    });
    // Réindexer le tableau
    $_SESSION['selected_seances'] = array_values($_SESSION['selected_seances']);
}

header('Content-Type: application/json');
echo json_encode(['selected_seances' => $_SESSION['selected_seances']]);
exit;
