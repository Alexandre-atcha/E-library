<?php
$host = 'sql110.infinityfree.com';
$user = 'if0_41313525';
$pass = 'Infinity2026'; // ton mot de passe réel
$db   = 'if0_41313525_elearning_db';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Erreur MySQL : " . $mysqli->connect_error);
} else {
    echo "Connexion MySQL réussie !";
}
?>