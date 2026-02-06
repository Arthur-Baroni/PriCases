<?php
// Script para receber o cadastro e gravar no banco MySQL

// CONFIGURAÇÕES DO BANCO - ajuste conforme seu ambiente (XAMPP/WAMP etc.)
$host   = 'localhost';
$usuario = 'root';      // ajuste se seu usuário for diferente
$senhaDb = '0000';          // coloque aqui a senha do MySQL se tiver
$banco  = 'pricases';

$mysqli = new mysqli($host, $usuario, $senhaDb, $banco);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><title>Erro</title></head><body>';
    echo '<h2>Erro ao conectar ao banco de dados.</h2>';
    echo '<p>Verifique as configurações em cadastrar_usuario.php.</p>';
    echo '</body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = $_POST['nome'] ?? '';
    $email    = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $senha    = $_POST['senha'] ?? '';

    // Validação simples
    if (trim($nome) === '' || trim($email) === '' || trim($senha) === '') {
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><title>Cadastro</title></head><body>';
        echo '<h2>Dados obrigatórios faltando.</h2>';
        echo '<p><a href="cadastro.php">Voltar para o cadastro</a></p>';
        echo '</body></html>';
        exit;
    }

    // Gera hash seguro da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare('INSERT INTO usuarios (nome, email, telefone, senha_hash) VALUES (?, ?, ?, ?)');
    if (!$stmt) {
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><title>Erro</title></head><body>';
        echo '<h2>Erro ao preparar comando SQL.</h2>';
        echo '<p>' . htmlspecialchars($mysqli->error, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '</body></html>';
        exit;
    }

    $stmt->bind_param('ssss', $nome, $email, $telefone, $senhaHash);

    if ($stmt->execute()) {
        echo <<<'HTML'
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro realizado | PriCases</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
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
        nav {
            margin-top: 0;
            display: flex;
            gap: 18px;
        }
        nav a {
            color: #FFFFFF;
            text-decoration: none;
            margin: 0;
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 10px 0 10px;
        }
        .message-box {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .message-box h2 {
            margin-top: 0;
            color: #C28161;
        }
        .message-box p {
            margin: 8px 0;
        }
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
        .message-box a:hover {
            box-shadow: 0 3px 10px #0004;
        }
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
            <a href="cadastro.php">Cadastro</a>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="message-box">
            <h2>Cadastro realizado com sucesso!</h2>
            <p>Você será redirecionado para a página de login em instantes.</p>
            <p><a href="login.php">Ir para o login agora</a></p>
        </div>
    </main>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
    <script>
        setTimeout(function(){ window.location.href = 'login.php'; }, 3000);
    </script>
</body>
</html>
HTML;
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><title>Erro</title></head><body>';
        echo '<h2>Erro ao salvar cadastro.</h2>';
        echo '<p>' . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><a href="cadastro.php">Voltar para o cadastro</a></p>';
        echo '</body></html>';
    }

    $stmt->close();
} else {
    // Se alguém acessar o script direto sem POST, redireciona para o formulário
    header('Location: cadastro.php');
    exit;
}

$mysqli->close();
