<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: inicial.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: catalogo.php');
    exit;
}

$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

$mysqli = new mysqli($host, $usuario, $senhaDb, $banco);

if (!$mysqli->connect_errno) {
    // Exclusão lógica: marcar como inativo
    $stmt = $mysqli->prepare('UPDATE produtos SET ativo = 0 WHERE id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();
}

header('Location: catalogo.php');
exit;
