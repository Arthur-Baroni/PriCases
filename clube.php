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
    <title>Clube da Case | PriCases</title>
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
            padding: 40px 20px 40px 20px;
        }
        .clube-header {
            max-width: 900px;
            margin: 0 auto 30px auto;
            text-align: center;
        }
        .clube-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(0,0,0,0.06);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #555;
            margin-bottom: 6px;
        }
        .clube-header h1 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
            color: #C28161;
        }
        .clube-header p {
            margin: 0;
            color: #555;
            line-height: 1.6;
        }
        .planos-container {
            max-width: 1100px;
            margin: 40px auto 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }
        .plano-card {
            background: #EEE2DB;
            border-radius: 14px;
            box-shadow: 0 2px 10px #0002;
            padding: 22px 20px 24px 20px;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .plano-nome {
            font-size: 1.2rem;
            font-weight: 700;
            color: #C28161;
            margin-bottom: 4px;
        }
        .plano-tagline {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 10px;
        }
        .plano-preco {
            font-size: 1.4rem;
            font-weight: 700;
            color: #C28161;
            margin-bottom: 8px;
        }
        .plano-preco span {
            font-size: 0.9rem;
            font-weight: 400;
            color: #555;
        }
        .plano-beneficios {
            list-style: none;
            padding: 0;
            margin: 12px 0 18px 0;
            font-size: 0.92rem;
            color: #444;
        }
        .plano-beneficios li {
            margin-bottom: 6px;
        }
        .plano-beneficios li::before {
            content: "✔ ";
            color: #C28161;
        }
        .plano-cta {
            margin-top: auto;
        }
        .btn-plano {
            display: inline-block;
            padding: 10px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            background: #C28161;
            color: #FFFFFF;
            box-shadow: 0 2px 6px #0003;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .btn-plano:hover {
            background: #C28161;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px #0004;
        }
        .observacao {
            max-width: 900px;
            margin: 30px auto 0 auto;
            font-size: 0.85rem;
            color: #666;
            text-align: center;
        }
        footer {
            background: #C28161;
            color: #FFFFFF;
            text-align: center;
            padding: 18px 0 10px 0;
            margin-top: 40px;
            flex-shrink: 0;
        }
        @media (max-width: 800px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 12px 16px;
            }
            .logo { font-size: 1.4rem; }
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
        <section class="clube-header">
            <div class="clube-tag">Exclusivo PriCases</div>
            <h1>Planos do Clube da Case</h1>
            <p>Escolha o plano que combina com o seu jeito de colecionar capinhas e receba vantagens especiais para deixar seu celular sempre de cara nova.</p>
        </section>
        <section class="planos-container" aria-label="Opções de planos do Clube da Case">
            <article class="plano-card">
                <div>
                    <div class="plano-nome">Essencial</div>
                    <div class="plano-tagline">Para quem está começando a amar capinhas</div>
                    <div class="plano-preco">R$ 19,90 <span>/ mês</span></div>
                    <ul class="plano-beneficios">
                        <li>1 cupom de desconto por mês</li>
                        <li>Acesso antecipado a algumas coleções</li>
                        <li>Lista de inspirações e combinações de cases</li>
                    </ul>
                </div>
                <div class="plano-cta">
                    <?php if ($logado): ?>
                        <a href="perfil.php" class="btn-plano">Quero o Essencial</a>
                    <?php else: ?>
                        <a href="cadastro.php" class="btn-plano">Criar conta para assinar</a>
                    <?php endif; ?>
                </div>
            </article>
            <article class="plano-card">
                <div>
                    <div class="plano-nome">Trendy</div>
                    <div class="plano-tagline">Para quem troca de capinha com frequência</div>
                    <div class="plano-preco">R$ 34,90 <span>/ mês</span></div>
                    <ul class="plano-beneficios">
                        <li>2 cupons de desconto por mês</li>
                        <li>Acesso antecipado a coleções limitadas</li>
                        <li>Participação em sorteios exclusivos</li>
                        <li>Suporte prioritário via WhatsApp</li>
                    </ul>
                </div>
                <div class="plano-cta">
                    <?php if ($logado): ?>
                        <a href="perfil.php" class="btn-plano">Quero o Trendy</a>
                    <?php else: ?>
                        <a href="cadastro.php" class="btn-plano">Criar conta para assinar</a>
                    <?php endif; ?>
                </div>
            </article>
            <article class="plano-card">
                <div>
                    <div class="plano-nome">Premium</div>
                    <div class="plano-tagline">Para quem é realmente viciado em cases</div>
                    <div class="plano-preco">R$ 59,90 <span>/ mês</span></div>
                    <ul class="plano-beneficios">
                        <li>3 cupons de desconto por mês</li>
                        <li>Acesso garantido a todas as coleções limitadas</li>
                        <li>Brinde surpresa em datas especiais</li>
                        <li>Convites para pré-vendas exclusivas</li>
                    </ul>
                </div>
                <div class="plano-cta">
                    <?php if ($logado): ?>
                        <a href="perfil.php" class="btn-plano">Quero o Premium</a>
                    <?php else: ?>
                        <a href="cadastro.php" class="btn-plano">Criar conta para assinar</a>
                    <?php endif; ?>
                </div>
            </article>
        </section>
        <div class="observacao">
            * Os valores e benefícios podem ser ajustados conforme campanhas e condições especiais da PriCases. Fale conosco pelo WhatsApp para mais detalhes sobre o Clube da Case.
        </div>
    </main>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
</body>
</html>
