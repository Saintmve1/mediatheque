<?php
session_start();

echo "<pre>";
echo "Session ID : " . session_id() . "\n";
echo "User ID    : " . ($_SESSION['user_id'] ?? 'NON CONNECTÉ') . "\n";
echo "User role  : " . ($_SESSION['user_role'] ?? 'aucun') . "\n";
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color:green'>Connecté !</p>";
    echo "<a href='documents/index'>Aller vers documents</a>";
} else {
    echo "<p style='color:red'>Non connecté</p>";
    echo "<a href='auth/login'>Se connecter</a>";
}