<?php
// Processa o cadastro de produto e grava na tabela 'produtos'

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro_produto.php');
    exit;
}

$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

$nome       = trim($_POST['nome-produto'] ?? '');
$categoria  = trim($_POST['categoria'] ?? '');
$marca      = trim($_POST['marca'] ?? '');
$modelo     = trim($_POST['modelo'] ?? '');
$precoStr   = trim($_POST['preco'] ?? '');
$imagem     = trim($_POST['imagem'] ?? '');
$descricao  = trim($_POST['descricao'] ?? '');

function renderMensagem(string $titulo, string $mensagem, bool $sucesso = false): void
{
    $tituloEsc   = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $mensagemEsc = htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8');
    $corTitulo   = $sucesso ? '#2e7d32' : '#b3261e';

    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto | PriCases</title>
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #FFFFFF;
            color: #222;
        }
        header {
            background: #DEBEAE;
            color: #C28161;
            padding: 18px 40px;
            box-shadow: 0 2px 8px #0001;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo img {
            height: 48px;
            display: block;
            transform: scale(3.6);
            transform-origin: left center;
        }
        nav { display: flex; gap: 18px; }
        nav a {
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 18px;
            border-radius: 999px;
            background: #C28161;
            box-shadow: 0 2px 6px #0003;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        nav a:hover {
            background: #C28161;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px #0004;
        }
        main {
            flex: 1 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 10px 0 10px;
        }
        .message-box {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 32px 24px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .message-box h2 {
            margin-top: 0;
            color: {$corTitulo};
        }
        .message-box p { margin: 8px 0; }
        .message-box a {
            color: #FFFFFF;
            background: #C28161;
            padding: 8px 16px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            box-shadow: 0 2px 6px #0003;
        }
        .message-box a:hover { box-shadow: 0 3px 10px #0004; }
        footer {
            background: #C28161;
            color: #FFFFFF;
            text-align: center;
            padding: 18px 0 10px 0;
            margin-top: 40px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a href="inicial.php"><img src="imagens/Logo.png" alt="PriCases"></a></div>
        <nav>
            <a href="inicial.php">Home</a>
            <a href="catalogo.php">Catálogo</a>
            <a href="cadastro_produto.php">Novo produto</a>
        </nav>
    </header>
    <main>
        <div class="message-box">
            <h2>{$tituloEsc}</h2>
            <p>{$mensagemEsc}</p>
            <p><a href="cadastro_produto.php">Voltar ao cadastro de produto</a></p>
        </div>
    </main>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
</body>
</html>
HTML;
}

// Valida categoria (aceita apenas os tipos esperados)
$categoriasValidas = ['capinhas', 'pulseiras', 'acessorios'];
if ($categoria === '' || !in_array($categoria, $categoriasValidas, true)) {
    $categoria = 'capinhas';
}

// Validação básica
if ($nome === '' || $precoStr === '' || $imagem === '' || $modelo === '' || ($categoria === 'capinhas' && $marca === '')) {
    renderMensagem('Erro no cadastro', 'Preencha todos os campos obrigatórios.', false);
    exit;
}

// Converte preço em string (ex: "R$ 39,90") para número decimal (39.90)
$apenasNumeros = preg_replace('/[^0-9,\.]/', '', $precoStr);
$apenasNumeros = str_replace(',', '.', $apenasNumeros);

if ($apenasNumeros === '' || !is_numeric($apenasNumeros)) {
    renderMensagem('Erro no cadastro', 'Preço inválido. Use o formato 39,90.', false);
    exit;
}

$preco = (float)$apenasNumeros;

$mysqli = new mysqli($host, $usuario, $senhaDb, $banco);

if ($mysqli->connect_errno) {
    renderMensagem('Erro', 'Erro ao conectar ao banco de dados.', false);
    exit;
}

$stmt = $mysqli->prepare('INSERT INTO produtos (nome, categoria, marca, modelo, preco, imagem_arquivo, descricao) VALUES (?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) {
    renderMensagem('Erro', 'Erro ao preparar comando SQL.', false);
    $mysqli->close();
    exit;
}

$stmt->bind_param('ssssdss', $nome, $categoria, $marca, $modelo, $preco, $imagem, $descricao);

if ($stmt->execute()) {
    renderMensagem('Produto cadastrado', 'O produto foi cadastrado com sucesso!', true);
} else {
    renderMensagem('Erro', 'Erro ao salvar o produto. Tente novamente.', false);
}

$stmt->close();
$mysqli->close();
