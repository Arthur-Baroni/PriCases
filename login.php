<?php
session_start();

// CONFIGURAÇÕES DO BANCO - mesmas de cadastrar_usuario.php
$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

function renderLoginForm(string $email = '', ?string $emailError = null, ?string $senhaError = null): void
{
    $emailEsc = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $emailErrorHtml = $emailError ? '<div class="error-msg">' . htmlspecialchars($emailError, ENT_QUOTES, 'UTF-8') . '</div>' : '';
    $senhaErrorHtml = $senhaError ? '<div class="error-msg">' . htmlspecialchars($senhaError, ENT_QUOTES, 'UTF-8') . '</div>' : '';
    $logado = isset($_SESSION['usuario_id']);

    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PriCases</title>
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
        body.page-enter {
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        body.page-enter.page-enter-active {
            opacity: 1;
        }
        body.page-leave {
            opacity: 0;
            transition: opacity 0.35s ease;
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
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            height: 48px;
            display: block;
            transform: scale(3.6);
            transform-origin: left center;
        }
        .nav-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        .menu-toggle {
            display: inline-block;
            background: #C28161;
            color: #FFFFFF;
            border: none;
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 6px #0003;
            letter-spacing: 0.03em;
        }
        .menu-toggle:hover {
            background: #C28161;
            box-shadow: 0 3px 10px #0004;
        }
        nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: absolute;
            top: 120%;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            padding: 12px 14px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
            border: 1px solid #DEBEAE;
            z-index: 10;
            min-width: 190px;
        }
        nav.fechado {
            display: none;
        }
        nav a {
            display: block;
            color: #C28161;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 7px 12px;
            border-radius: 999px;
            background: transparent;
            box-shadow: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s, color 0.2s;
        }
        nav a:hover {
            background: #C28161;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px #0002;
        }
        main {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 10px 0 10px;
        }
        .login-box {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .login-box h2 {
            margin-top: 0;
            color: #C28161;
        }
        .login-form label {
            display: block;
            margin: 12px 0 4px 0;
            font-weight: 500;
        }
        .login-form input {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 4px;
            font-size: 1rem;
        }
        .login-form button {
            background: #C28161;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        .login-form button:hover {
            background: #C28161;
            box-shadow: 0 3px 10px #0003;
        }
        .login-extra {
            margin-top: 12px;
            font-size: 0.9rem;
        }
        .login-extra a {
            color: #C28161;
            text-decoration: none;
            font-weight: 600;
        }
        .login-extra a:hover {
            text-decoration: underline;
        }
        .error-msg {
            color: #b3261e;
            font-size: 0.85rem;
            text-align: left;
            margin-bottom: 8px;
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
        <div class="nav-wrapper">
            <button class="menu-toggle" id="btnMenu">☰ Menu</button>
            <nav id="menuPrincipal">
                <a href="inicial.php">Home</a>
                <a href="catalogo.php">Catálogo</a>
                <a href="clube.php">Clube da Case</a>
                <?php if (!$logado): ?>
                    <a href="cadastro.php">Cadastro</a>
                <?php else: ?>
                    <a href="perfil.php">Meu perfil</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
        <div class="login-box">
            <h2>Login</h2>
            <form class="login-form" method="post" action="login.php">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="{$emailEsc}">
                {$emailErrorHtml}
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
                {$senhaErrorHtml}
                <button type="submit">Entrar</button>
            </form>
            <?php if (!$logado): ?>
            <div class="login-extra">
                Não tem conta? <a href="cadastro.php">Cadastre-se</a>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
    <script>
        document.body.classList.add('page-enter');
        requestAnimationFrame(function() {
            document.body.classList.add('page-enter-active');
        });

        document.querySelectorAll('a[href$=".php"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                if (!href || href.startsWith('#')) return;
                e.preventDefault();
                document.body.classList.remove('page-enter', 'page-enter-active');
                document.body.classList.add('page-leave');
                setTimeout(function() {
                    window.location.href = href;
                }, 300);
            });
        });

        // Menu dropdown
        (function() {
            var btnMenu = document.getElementById('btnMenu');
            var nav = document.getElementById('menuPrincipal');
            if (!btnMenu || !nav) return;

            nav.classList.add('fechado');

            btnMenu.addEventListener('click', function() {
                var fechado = nav.classList.toggle('fechado');
                btnMenu.textContent = fechado ? '☰ Menu' : '✕ Fechar';
            });
        })();
    </script>
</body>
</html>
HTML;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Primeira vez que abre o login pelo PHP (se quiser usar login.php direto)
    renderLoginForm();
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if ($email === '' || $senha === '') {
    $emailError = $email === '' ? 'Informe o email.' : null;
    $senhaError = $senha === '' ? 'Informe a senha.' : null;
    renderLoginForm($email, $emailError, $senhaError);
    exit;
}

$mysqli = new mysqli($host, $usuario, $senhaDb, $banco);

if ($mysqli->connect_errno) {
    // Erro de conexão genérico mostrado abaixo do email
    renderLoginForm($email, 'Erro ao conectar ao banco de dados.', null);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ? LIMIT 1');
if (!$stmt) {
    renderLoginForm($email, 'Erro ao preparar consulta no banco de dados.', null);
    $mysqli->close();
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

$usuario = $result->fetch_assoc();

// Verifica se o email existe
if (!$usuario) {
    renderLoginForm($email, 'Email incorreto.', null);
    $stmt->close();
    $mysqli->close();
    exit;
}

// Email existe, verifica senha
if (!password_verify($senha, $usuario['senha_hash'])) {
    renderLoginForm($email, null, 'Senha incorreta.');
    $stmt->close();
    $mysqli->close();
    exit;
}

// Login OK: cria sessão e redireciona para a página inicial
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nome'] = $usuario['nome'];
// Marca como administrador se for o email de admin
$_SESSION['is_admin'] = ($email === 'admin@pricases.com');

$stmt->close();
$mysqli->close();

header('Location: inicial.php');
exit;
