<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$nomeSessao = $_SESSION['usuario_nome'] ?? '';
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

$dados    = null;
$erro     = '';
$sucesso  = '';

$mysqli = @new mysqli($host, $usuario, $senhaDb, $banco);
if (!$mysqli->connect_errno) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $novoNome     = trim($_POST['nome'] ?? '');
        $novoEmail    = trim($_POST['email'] ?? '');
        $novoTelefone = trim($_POST['telefone'] ?? '');

        if ($novoNome === '' || $novoEmail === '') {
            $erro = 'Nome e e-mail são obrigatórios.';
        } elseif (!filter_var($novoEmail, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe um e-mail válido.';
        } else {
            $stmtUpdate = $mysqli->prepare('UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ? LIMIT 1');
            if ($stmtUpdate) {
                $stmtUpdate->bind_param('sssi', $novoNome, $novoEmail, $novoTelefone, $usuarioId);
                if ($stmtUpdate->execute()) {
                    $sucesso = 'Dados atualizados com sucesso.';
                    $_SESSION['usuario_nome'] = $novoNome;
                } else {
                    $erro = 'Não foi possível salvar as alterações. Tente novamente.';
                }
                $stmtUpdate->close();
            } else {
                $erro = 'Erro interno ao preparar a atualização.';
            }
        }
    }

    $stmt = $mysqli->prepare('SELECT nome, email, telefone, criado_em FROM usuarios WHERE id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dados = $resultado->fetch_assoc();
        $stmt->close();
    }

    $mysqli->close();
}

function h($str) {
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

$nome     = $dados['nome']     ?? $nomeSessao;
$email    = $dados['email']    ?? '';
$telefone = $dados['telefone'] ?? '';
$criadoEm = $dados['criado_em'] ?? '';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | PriCases</title>
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
            margin-left: -20px;
            margin-top: -6px;
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
        .icon-user {
            margin-right: 6px;
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
            align-items: center;
            justify-content: center;
            padding: 40px 10px 40px 10px;
        }
        .perfil-box {
            background: #EEE2DB;
            border-radius: 16px;
            box-shadow: 0 6px 18px #0003;
            padding: 28px 28px 32px 28px;
            max-width: 520px;
            width: 100%;
            display: grid;
            grid-template-columns: 140px 1fr;
            column-gap: 24px;
            row-gap: 8px;
        }
        .perfil-avatar {
            grid-row: 1 / span 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .avatar-circle {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: linear-gradient(135deg, #C28161, #DEBEAE);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2.2rem;
            font-weight: 700;
            box-shadow: 0 3px 10px #0004;
        }
        .perfil-role {
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(0,0,0,0.07);
            color: #444;
        }
        .perfil-header {
            grid-column: 2 / 3;
        }
        .perfil-header h2 {
            margin: 0 0 4px 0;
            color: #C28161;
            font-size: 1.4rem;
        }
        .perfil-header p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }
        .perfil-dados {
            grid-column: 2 / 3;
            margin-top: 12px;
        }
        .perfil-item {
            margin-bottom: 10px;
        }
        .perfil-item span.label {
            display: block;
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .perfil-item span.valor {
            font-size: 0.98rem;
            font-weight: 500;
        }
        .perfil-edit-toggle {
            margin-top: 8px;
            background: transparent;
            border: 1px solid #C28161;
            color: #C28161;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .perfil-edit-toggle:hover {
            background: #C28161;
            color: #FFFFFF;
            box-shadow: 0 2px 6px #0003;
            transform: translateY(-1px);
        }
        .perfil-rodape {
            grid-column: 1 / -1;
            margin-top: 16px;
            font-size: 0.85rem;
            text-align: center;
            color: #555;
        }
        .perfil-form {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid rgba(0,0,0,0.08);
            display: none;
        }
        .perfil-form.ativo {
            display: block;
        }
        .perfil-form h3 {
            margin: 0 0 8px 0;
            font-size: 1rem;
            color: #C28161;
        }
        .perfil-form label {
            display: block;
            margin: 10px 0 4px 0;
            font-size: 0.85rem;
            color: #555;
        }
        .perfil-form input {
            width: 100%;
            padding: 7px 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .perfil-form button {
            margin-top: 12px;
            background: #C28161;
            color: #FFFFFF;
            border: none;
            border-radius: 999px;
            padding: 8px 18px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 6px #0003;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .perfil-form button:hover {
            background: #C28161;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px #0004;
        }
        .alerta {
            margin-top: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .alerta.sucesso {
            background: #d7f5e3;
            color: #1f7a3a;
        }
        .alerta.erro {
            background: #fde2e0;
            color: #b3261e;
        }
        @media (max-width: 640px) {
            .perfil-box {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .perfil-avatar {
                grid-row: auto;
                margin-bottom: 12px;
            }
            .perfil-header,
            .perfil-dados,
            .perfil-rodape {
                grid-column: 1 / -1;
            }
            .perfil-item {
                text-align: left;
            }
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
                    <?php if ($isAdmin): ?>
                        <a href="cadastro_produto.php">Cadastrar produto</a>
                    <?php endif; ?>
                    <a href="logout.php">Sair</a>
                </nav>
            </div>
    </header>
    <main>
        <div class="perfil-box">
            <div class="perfil-avatar">
                <div class="avatar-circle"><?php echo strtoupper(substr(h($nome), 0, 1)); ?></div>
                <div class="perfil-role"><?php echo $isAdmin ? 'Administrador' : 'Cliente PriCases'; ?></div>
            </div>
            <div class="perfil-header">
                <h2><?php echo h($nome); ?></h2>
                <p><?php echo h($email); ?></p>
            </div>
            <div class="perfil-dados">
                <div class="perfil-item">
                    <span class="label">Telefone</span>
                    <span class="valor"><?php echo h($telefone ?: 'Não informado'); ?></span>
                </div>
                <div class="perfil-item">
                    <span class="label">Cliente desde</span>
                    <span class="valor"><?php echo h($criadoEm); ?></span>
                </div>
                <button type="button" class="perfil-edit-toggle" id="btnEditarPerfil">Editar dados</button>
                <?php if (!empty($sucesso) || !empty($erro)): ?>
                    <div class="alerta <?php echo !empty($sucesso) ? 'sucesso' : 'erro'; ?>">
                        <?php echo h($sucesso !== '' ? $sucesso : $erro); ?>
                    </div>
                <?php endif; ?>
                <form class="perfil-form<?php echo (!empty($sucesso) || !empty($erro) || $_SERVER['REQUEST_METHOD'] === 'POST') ? ' ativo' : ''; ?>" method="post" action="perfil.php">
                    <h3>Editar dados principais</h3>
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required value="<?php echo h($nome); ?>">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required value="<?php echo h($email); ?>">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" value="<?php echo h($telefone); ?>">
                    <button type="submit">Salvar alterações</button>
                </form>
            </div>
            <div class="perfil-rodape">
                Aqui você pode manter seus dados principais sempre atualizados.
            </div>
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

        // Botão para ativar/ocultar o formulário de edição de perfil
        (function() {
            var btn = document.getElementById('btnEditarPerfil');
            var form = document.querySelector('.perfil-form');
            if (!btn || !form) return;

            // Ajusta rótulo inicial se o formulário já vier ativo (após POST)
            if (form.classList.contains('ativo')) {
                btn.textContent = 'Fechar edição';
            }

            btn.addEventListener('click', function() {
                var ativo = form.classList.toggle('ativo');
                btn.textContent = ativo ? 'Fechar edição' : 'Editar dados';
                if (ativo) {
                    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        })();
    </script>
</body>
</html>
