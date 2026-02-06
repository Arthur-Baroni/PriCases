<?php
session_start();
$logado = isset($_SESSION['usuario_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | PriCases</title>
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
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #C28161;
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
            margin-top: 0;
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
            margin: 0;
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
        .cadastro-box {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .cadastro-box h2 {
            margin-top: 0;
            color: #C28161;
        }
        .cadastro-form label {
            display: block;
            margin: 12px 0 4px 0;
            font-weight: 500;
        }
        .cadastro-form input {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 12px;
            font-size: 1rem;
        }
        .cadastro-form button {
            background: #C28161;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .cadastro-form button:hover {
            background: #C28161;
            box-shadow: 0 3px 10px #0003;
        }
        footer {
            background: #C28161;
            color: #FFFFFF;
            text-align: center;
            padding: 18px 0 10px 0;
            margin-top: 40px;
            flex-shrink: 0;
        }
        @media (max-width: 600px) {
            header h1 { font-size: 1.3rem; }
            .cadastro-box { padding: 18px 5px; }
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
                <?php if ($logado): ?>
                    <a href="perfil.php"><span class="icon-user">👤</span>Meu perfil</a>
                    <a href="logout.php">Sair</a>
                <?php else: ?>
                    <a href="cadastro.php">Cadastro</a>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
        <div class="cadastro-box">
            <h2>Cadastro</h2>
            <form class="cadastro-form" method="post" action="cadastrar_usuario.php">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone">
                <button type="submit">Cadastrar</button>
            </form>
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

        (function() {
            var btn = document.getElementById('btnMenu');
            var nav = document.getElementById('menuPrincipal');
            if (!btn || !nav) return;

            nav.classList.add('fechado');
            btn.textContent = '☰ Menu';

            btn.addEventListener('click', function() {
                var fechado = nav.classList.toggle('fechado');
                btn.textContent = fechado ? '☰ Menu' : '✕ Fechar';
            });
        })();
    </script>
</body>
</html>
