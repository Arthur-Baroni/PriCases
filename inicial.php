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
    <title>PriCases - Capinhas e Acessórios</title>
    <style>
        html {
            scroll-behavior: smooth;
            height: 100%;
        }
        body {
            margin: 0;
            padding: 0;
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
            margin-left: -20px; /* aproxima a logo da borda esquerda sem mudar o tamanho */
            margin-top: -6px;  /* sobe um pouco a logo sem alterar o tamanho */
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
            padding-bottom: 40px;
        }
        .hero {
            max-width: 1200px;
            margin: 30px auto 40px auto;
            padding: 0 5vw 30px 5vw;
            display: flex;
            gap: 32px;
            align-items: center;
        }
        .hero-text {
            flex: 1.2;
        }
        .hero-text h2 {
            font-size: 2rem;
            margin: 0 0 12px 0;
            color: #C28161;
        }
        .hero-text p {
            font-size: 1rem;
            line-height: 1.6;
            color: #444;
            margin: 0 0 20px 0;
        }
        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .btn-cta {
            display: inline-block;
            padding: 10px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .btn-cta.primary {
            background: #C28161;
            color: #FFFFFF;
        }
        .btn-cta.primary:hover {
            background: #C28161;
            box-shadow: 0 3px 10px #0003;
        }
        .btn-cta.secondary {
            background: #FFFFFF;
            color: #C28161;
            border: 1px solid #C28161;
        }
        .btn-cta.secondary:hover {
            background: #EEE2DB;
        }
        .hero-image {
            flex: 1;
            min-height: 220px;
            border-radius: 18px;
            background: #EEE2DB;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #C28161;
            font-weight: 600;
            box-shadow: 0 6px 18px #0003;
        }
        .sobre-loja {
            max-width: 1000px;
            margin: 0 auto 40px auto;
            padding: 0 5vw;
            text-align: left;
        }
        .sobre-loja h3 {
            margin: 0 0 10px 0;
            font-size: 1.4rem;
            color: #C28161;
        }
        .sobre-loja p {
            margin: 0 0 8px 0;
            color: #555;
            line-height: 1.6;
        }
        .produtos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 28px;
            padding: 0 5vw 40px 5vw;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }
        .produto {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 18px;
            text-align: center;
            opacity: 1;
            transform: translateY(0);
            animation: produto-loop 8s ease-in-out infinite;
            animation-fill-mode: both;
        }
        .produto:nth-child(2) {
            animation-delay: 1s;
        }
        .produto:nth-child(3) {
            animation-delay: 2s;
        }
        .produto:nth-child(4) {
            animation-delay: 3s;
        }
        @keyframes produto-loop {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            15% {
                opacity: 1;
                transform: translateY(0);
            }
            45% {
                opacity: 1;
                transform: translateY(0);
            }
            60% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 0;
                transform: translateY(10px);
            }
        }
        .produto img {
            width: 100%;
            max-width: 160px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .produto h2 {
            font-size: 1.1rem;
            margin: 8px 0 4px 0;
        }
        .produto p {
            color: #666;
            font-size: 0.97rem;
            margin: 0 0 8px 0;
        }
        .produto .preco {
            color: #C28161;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .clube-explica {
            max-width: 1000px;
            margin: 0 auto 30px auto;
            padding: 0 5vw;
            text-align: center;
        }
        .clube-explica h3 {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: #C28161;
        }
        .clube-explica p {
            color: #555;
            line-height: 1.7;
        }
        .clube-section {
            max-width: 1000px;
            margin: 0 auto 40px auto;
            padding: 22px 5vw 26px 5vw;
            text-align: left;
            background: linear-gradient(135deg, #EEE2DB, #DEBEAE);
            border-radius: 18px;
            box-shadow: 0 4px 14px #0002;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
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
            margin-bottom: 4px;
        }
        .clube-textos {
            flex: 1 1 260px;
        }
        .clube-section h3 {
            margin: 4px 0 6px 0;
            font-size: 1.6rem;
            color: #C28161;
        }
        .clube-section p {
            margin: 0 0 6px 0;
            color: #4a4a4a;
            line-height: 1.6;
        }
        .clube-beneficios {
            margin-top: 6px;
            font-size: 0.9rem;
            color: #555;
        }
        .clube-beneficios span {
            margin-right: 10px;
        }
        .clube-acao {
            flex: 0 0 auto;
        }
        .clube-section .btn-clube {
            display: inline-block;
            padding: 10px 24px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            background: #C28161;
            color: #FFFFFF;
            box-shadow: 0 2px 6px #0003;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .clube-section .btn-clube:hover {
            background: #C28161;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px #0004;
        }
        @media (max-width: 800px) {
            .clube-section {
                text-align: center;
                justify-content: center;
            }
            .clube-textos {
                flex-basis: 100%;
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
        @media (max-width: 800px) {
            header {
                padding: 12px 16px;
            }
            .logo { font-size: 1.4rem; }
            .nav-wrapper {
                gap: 8px;
            }
            .hero {
                flex-direction: column;
                text-align: center;
            }
            .hero-text {
                order: 1;
            }
            .hero-image {
                order: 0;
                width: 100%;
            }
            .hero-buttons { justify-content: center; }
            .produtos { padding: 0 2vw 30px 2vw; }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a href="inicial.php"><img src="imagens/Logo.png" alt="PriCases"></a></div>
        <div class="nav-wrapper">
            <button class="menu-toggle" id="btnMenu">Menu</button>
            <nav id="menuPrincipal">
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
        <section class="hero" id="topo">
            <div class="hero-text">
                <h2>Capinhas com a sua cara</h2>
                <p>Na PriCases você encontra capinhas modernas, resistentes e cheias de personalidade para deixar o seu celular protegido e estiloso. Trabalhamos com diversos modelos de aparelhos e atualizamos nosso catálogo com novidades toda semana.</p>
                <div class="hero-buttons">
                    <a href="catalogo.php" class="btn-cta primary">Ver catálogo completo</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="imagens/Logo.png" alt="PriCases">
            </div>
        </section>
        <section class="sobre-loja" id="sobre">
            <h3>Sobre a PriCases</h3>
            <p>Somos uma loja especializada em capinhas e acessórios para celular, com foco em qualidade, estilo e bom atendimento. Cada peça é escolhida com carinho para combinar proteção e beleza no dia a dia.</p>
            <p>Atendemos diversos modelos de celulares e estamos sempre em busca das últimas tendências para que você encontre aqui a capinha perfeita para o seu aparelho.</p>
        </section>
        <section class="produtos" aria-label="Destaques da PriCases">
            <div class="produto">
                <img src="imagens/tons_de_bordo.jpg" alt="Capinha destaque 1">
                <h2>Tons de Bordô</h2>
                <p>Modelos em diferentes tons de bordô para um visual sofisticado e elegante.</p>
                <div class="preco">Sob consulta</div>
            </div>
            <div class="produto">
                <img src="imagens/tons_de_cinza.jpg" alt="Capinha destaque 2">
                <h2>Tons de Cinza</h2>
                <p>Modelos em diferentes tons de cinza para um visual sóbrio e elegante.</p>
                <div class="preco">A partir de R$ 29,90</div>
            </div>
            <div class="produto">
                <img src="imagens/terra-cota_ip15promax.jpeg" alt="Capinha destaque 3">
                <h2>Terra Cota</h2>
                <p>Elegantes e discretas, perfeitas para quem gosta de tons terrosos.</p>
                <div class="preco">A partir de R$ 34,90</div>
            </div>
             <div class="produto">
                <img src="imagens/estelar.jpg" alt="Capinha destaque 4">
                <h2>Estelar</h2>
                <p>Designs inspirados no cosmos para um visual único e moderno.</p>
                <div class="preco">A partir de R$ 34,90</div>
            </div>
        </section>
        <section class="clube-section" aria-label="Clube da Case">
            <div class="clube-textos">
                <div class="clube-tag">Exclusivo PriCases</div>
                <h3>Clube da Case</h3>
                <p>Entre para o nosso clube e seja o primeiro a saber das novas coleções, promoções e capinhas especiais feitas para quem ama personalizar o celular.</p>
                <div class="clube-beneficios">
                    <span>✔ Descontos selecionados</span>
                    <span>✔ Novidades em primeira mão</span>
                    <span>✔ Inspirações de estilos</span>
                </div>
            </div>
            <div class="clube-acao">
                <a href="clube.php" class="btn-clube">Venha fazer parte</a>
            </div>
        </section>
        <section class="clube-explica" aria-label="O que é o Clube da Case">
            <h3>O que é o Clube da Case?</h3>
            <p>O Clube da Case é o cantinho especial da PriCases para quem ama capinhas. Ao entrar para o clube, você recebe novidades em primeira mão, fica por dentro das coleções limitadas e aproveita condições exclusivas pensadas para os clientes mais fiéis.</p>
        </section>
    </main>
    <footer id="contato">
        <div>
            PriCases &copy; 2026 | WhatsApp: (17) 99188-4681 |
            <a href="https://www.instagram.com/pri_casesrp/" target="_blank" rel="noopener noreferrer" style="color: #FFFFFF; text-decoration: underline;">
            https://www.instagram.com/pri_casesrp/
            </a>
        </div>
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

        // Botão que guarda/mostra os outros botões do menu (desktop e mobile)
        (function() {
            var btn = document.getElementById('btnMenu');
            var nav = document.getElementById('menuPrincipal');
            if (!btn || !nav) return;

            // Começa fechado em qualquer tamanho de tela
            nav.classList.add('fechado');
            btn.textContent = '☰ Menu';

            btn.addEventListener('click', function() {
                var fechado = nav.classList.toggle('fechado');
                btn.textContent = fechado ? '☰ Menu' : '✕ Fechar';
            });
        })();

        // Faz os cards de produtos trocarem de posição periodicamente
        (function() {
            var container = document.querySelector('.produtos');
            if (!container) return;

            setInterval(function() {
                var itens = container.querySelectorAll('.produto');
                if (itens.length > 1) {
                    // Move o último card para a primeira posição
                    container.insertBefore(itens[itens.length - 1], itens[0]);
                }
            }, 8000); // mesmo tempo aproximado da animação CSS
        })();
    </script>
</body>
</html>
