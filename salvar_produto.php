<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: inicial.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catalogo.php');
    exit;
}

$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

$id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nome      = trim($_POST['nome'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$marca     = trim($_POST['marca'] ?? '');
$modelo    = trim($_POST['modelo'] ?? '');
$precoStr  = trim($_POST['preco'] ?? '');
$imagem    = trim($_POST['imagem'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

// Valida categoria
$categoriasValidas = ['capinhas', 'pulseiras', 'acessorios'];
if ($categoria === '' || !in_array($categoria, $categoriasValidas, true)) {
    $categoria = 'capinhas';
}

// Modelo sempre obrigatório, marca só para capinhas
if ($id <= 0 || $nome === '' || $precoStr === '' || $imagem === '' || $modelo === '' || ($categoria === 'capinhas' && $marca === '')) {
    header('Location: editar_produto.php?id=' . $id);
    exit;
}

// Converte preço em string para número decimal
$apenasNumeros = preg_replace('/[^0-9,\.]/', '', $precoStr);
$apenasNumeros = str_replace(',', '.', $apenasNumeros);

if ($apenasNumeros === '' || !is_numeric($apenasNumeros)) {
    header('Location: editar_produto.php?id=' . $id);
    exit;
}

$preco = (float)$apenasNumeros;

$mysqli = new mysqli($host, $usuario, $senhaDb, $banco);

if ($mysqli->connect_errno) {
    header('Location: editar_produto.php?id=' . $id);
    exit;
}

$stmt = $mysqli->prepare('UPDATE produtos SET nome = ?, categoria = ?, marca = ?, modelo = ?, preco = ?, imagem_arquivo = ?, descricao = ? WHERE id = ?');
if (!$stmt) {
    $mysqli->close();
    header('Location: editar_produto.php?id=' . $id);
    exit;
}

$stmt->bind_param('ssssdssi', $nome, $categoria, $marca, $modelo, $preco, $imagem, $descricao, $id);
$stmt->execute();

$stmt->close();
$mysqli->close();

header('Location: catalogo.php');
exit;
