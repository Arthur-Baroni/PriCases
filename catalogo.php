<?php
session_start();
$logado = isset($_SESSION['usuario_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Carrega produtos do banco para montar o catálogo dinamicamente
$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

$produtosPorMarca = [
    'iphone'   => [],
    'samsung'  => [],
    'xiaomi'   => [],
    'motorola' => [],
    'outros'   => [],
];

$modelosPorMarca = [
    'iphone'   => [],
    'samsung'  => [],
    'xiaomi'   => [],
    'motorola' => [],
    'outros'   => [],
];

// Arrays para pulseiras (por modelo) e acessórios
$pulseirasPorModelo = [];
$modelosPulseiras   = [];
$produtosAcessorios = [];

$mysqli = @new mysqli($host, $usuario, $senhaDb, $banco);

if (!$mysqli->connect_errno) {
    // Capinhas (por marca/modelo)
    $sql = "SELECT id, nome, marca, modelo, preco, imagem_arquivo FROM produtos WHERE ativo = 1 AND categoria = 'capinhas'";
    if ($resultado = $mysqli->query($sql)) {
        while ($row = $resultado->fetch_assoc()) {
            $marca  = $row['marca'];
            $modelo = $row['modelo'];
            if (!isset($produtosPorMarca[$marca])) {
                $produtosPorMarca[$marca] = [];
                $modelosPorMarca[$marca]  = [];
            }
            $produtosPorMarca[$marca][] = [
                'id'     => (int)$row['id'],
                'nome'   => $row['nome'],
                'modelo' => $modelo,
                'preco'  => 'R$ ' . number_format((float)$row['preco'], 2, ',', '.'),
                'img'    => 'imagens/' . $row['imagem_arquivo'],
            ];
            if (!in_array($modelo, $modelosPorMarca[$marca], true)) {
                $modelosPorMarca[$marca][] = $modelo;
            }
        }
        $resultado->free();
    }

    // Pulseiras (agrupadas por modelo, independente da marca)
    $sql = "SELECT id, nome, modelo, preco, imagem_arquivo FROM produtos WHERE ativo = 1 AND categoria = 'pulseiras'";
    if ($resultado = $mysqli->query($sql)) {
        while ($row = $resultado->fetch_assoc()) {
            $modelo = $row['modelo'];
            if (!isset($pulseirasPorModelo[$modelo])) {
                $pulseirasPorModelo[$modelo] = [];
                $modelosPulseiras[]          = $modelo;
            }
            $pulseirasPorModelo[$modelo][] = [
                'id'    => (int)$row['id'],
                'nome'  => $row['nome'],
                'preco' => 'R$ ' . number_format((float)$row['preco'], 2, ',', '.'),
                'img'   => 'imagens/' . $row['imagem_arquivo'],
            ];
        }
        $resultado->free();
    }

    // Acessórios
    $sql = "SELECT id, nome, preco, imagem_arquivo FROM produtos WHERE ativo = 1 AND categoria = 'acessorios'";
    if ($resultado = $mysqli->query($sql)) {
        while ($row = $resultado->fetch_assoc()) {
            $produtosAcessorios[] = [
                'id'    => (int)$row['id'],
                'nome'  => $row['nome'],
                'preco' => 'R$ ' . number_format((float)$row['preco'], 2, ',', '.'),
                'img'   => 'imagens/' . $row['imagem_arquivo'],
            ];
        }
        $resultado->free();
    }

    $mysqli->close();
}

$jsModelos          = json_encode($modelosPorMarca, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$jsProdutos         = json_encode($produtosPorMarca, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$jsPulseirasModelos = json_encode($modelosPulseiras, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$jsPulseirasPorModelo = json_encode($pulseirasPorModelo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$jsAcessorios       = json_encode($produtosAcessorios, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | PriCases</title>
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
        .layout-catalogo {
            flex: 1 0 auto;
            display: flex;
            max-width: 1200px;
            margin: 40px auto 0 auto;
            gap: 16px;
        }
        .categorias-produto {
            width: 200px;
            background: #DEBEAE;
            border-radius: 12px;
            padding: 24px 0;
            align-self: flex-start;
            box-shadow: 0 2px 8px #0002;
        }
        .categorias-produto h2 {
            text-align: center;
            color: #C28161;
            font-size: 1.1rem;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .categorias-produto ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .categorias-produto li {
            padding: 10px 20px;
            cursor: pointer;
            color: #444;
            transition: background 0.2s, color 0.2s;
        }
        .categorias-produto li:hover,
        .categorias-produto li.selected {
            background: #C28161;
            color: #FFFFFF;
        }
        .box-principal-container {
            flex: 1;
        }
        .box-principal {
            display: none;
        }
        .box-principal.ativo {
            display: block;
        }
        .catalogo-container {
            display: flex;
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            min-height: 500px;
        }
        .opcoes-celular {
            width: 220px;
            border-right: 1px solid #DEBEAE;
            padding: 32px 0 32px 0;
            background: #DEBEAE;
            border-radius: 12px 0 0 12px;
        }
        .opcoes-celular h2 {
            text-align: center;
            color: #C28161;
            font-size: 1.2rem;
            margin-top: 0;
        }
        .opcoes-celular ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .opcoes-celular li {
            padding: 12px 24px;
            cursor: pointer;
            color: #444;
            border-radius: 6px 0 0 6px;
            transition: background 0.2s, color 0.2s;
        }
        .opcoes-celular li:hover, .opcoes-celular li.selected {
            background: #C28161;
            color: #FFFFFF;
        }
        .modelos-titulo {
            margin: 24px 24px 8px 24px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #C28161;
        }
        #lista-modelos {
            list-style: none;
            padding: 0;
            margin: 0 0 0 0;
            max-height: 220px; /* altura máxima da lista de modelos */
            overflow-y: auto;  /* permite rolar para ver mais modelos */
        }
        #lista-modelos li {
            padding: 6px 24px;
            font-size: 0.9rem;
            color: #333;
        }
        .produtos-celular {
            flex: 1;
            padding: 32px 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            align-content: flex-start;
            justify-content: flex-start;
            max-height: 765px;      /* limita a altura da área de produtos */
            overflow-y: auto;       /* rolagem interna para muitos produtos */
        }
        .produto {
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0001;
            padding: 18px;
            text-align: center;
            width: 180px;
            transition: transform 0.2s;
            opacity: 0;
            transform: translateY(15px);
            animation: produto-slide-in 0.80s ease-out forwards;
        }
        .produto img {
            width: 100%;
            max-width: 120px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .produto h3 {
            font-size: 1rem;
            margin: 8px 0 4px 0;
        }
        .produto .preco {
            color: #C28161;
            font-weight: bold;
            font-size: 1rem;
        }
        .acoes-admin {
            margin-top: 10px;
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        .btn-admin {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            background: #888;
        }
        .btn-admin.excluir {
            background: #b3261e;
        }
        @keyframes produto-slide-in {
            0% {
                opacity: 0;
                transform: translateY(15px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
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
        .cta-cadastro {
            max-width: 1200px;
            margin: 20px auto 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
        }
        .cta-cadastro a {
            background: #C28161;
            color: #FFFFFF;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 6px #0003;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .cta-cadastro a:hover {
            background: #C28161;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px #0004;
        }
        @media (max-width: 900px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 12px 16px;
            }
            .logo { font-size: 1.4rem; }
            nav { flex-wrap: wrap; }
            .layout-catalogo {
                flex-direction: column;
                max-width: 100%;
                margin: 20px 10px 0 10px;
            }
            .categorias-produto {
                width: 100%;
                display: flex;
                flex-direction: column;
                border-radius: 12px;
            }
            .catalogo-container { flex-direction: column; }
            .opcoes-celular { width: 100%; border-radius: 12px 12px 0 0; border-right: none; border-bottom: 1px solid #eee; }
            .produtos-celular {
                padding: 24px 10px;
                max-height: 360px;  /* um pouco menor em telas baixas */
                overflow-y: auto;
            }
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
    <div class="cta-cadastro">
        <?php if (!$logado): ?>
            <a href="cadastro.php">Não tem conta? Cadastre-se</a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <a href="cadastro_produto.php">Cadastrar novo produto</a>
        <?php endif; ?>
    </div>
    <div class="layout-catalogo">
        <aside class="categorias-produto">
            <h2>Categoria</h2>
            <ul id="lista-categorias">
                <li class="selected" data-categoria="capinhas">Capinhas</li>
                <li data-categoria="pulseiras">Pulseiras</li>
                <li data-categoria="acessorios">Acessórios</li>
            </ul>
        </aside>
        <div class="box-principal-container">
            <div id="box-capinhas" class="box-principal ativo">
                <div class="catalogo-container">
                    <aside class="opcoes-celular">
                        <h2>Celulares</h2>
                        <ul id="lista-celulares">
                            <li class="selected" data-modelo="iphone">iPhone</li>
                            <li data-modelo="samsung">Samsung</li>
                            <li data-modelo="xiaomi">Xiaomi</li>
                            <li data-modelo="motorola">Motorola</li>
                            <li data-modelo="outros">Outros</li>
                        </ul>
                        <div class="modelos-titulo">Modelos disponíveis</div>
                        <ul id="lista-modelos"></ul>
                    </aside>
                    <section class="produtos-celular" id="produtos-celular">
                        <!-- Produtos serão exibidos aqui -->
                    </section>
                </div>
            </div>
            <div id="box-pulseiras" class="box-principal">
                <div class="catalogo-container">
                    <aside class="opcoes-celular">
                        <h2>Modelos</h2>
                        <ul id="lista-modelos-pulseiras"></ul>
                    </aside>
                    <section class="produtos-celular" id="produtos-pulseiras">
                        <!-- Pulseiras serão exibidas aqui -->
                    </section>
                </div>
            </div>
            <div id="box-acessorios" class="box-principal">
                <div class="catalogo-container">
                    <section class="produtos-celular" id="produtos-acessorios">
                        <!-- Acessórios serão exibidos aqui -->
                    </section>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
    <script>
        // Dados vindos do banco (montados em PHP)
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
        const modelosPorMarca = <?php echo $jsModelos ?: '{}'; ?>;
        const produtosPorMarca = <?php echo $jsProdutos ?: '{}'; ?>;
        const modelosPulseiras = <?php echo $jsPulseirasModelos ?: '[]'; ?>;
        const pulseirasPorModelo = <?php echo $jsPulseirasPorModelo ?: '{}'; ?>;
        const produtosAcessorios = <?php echo $jsAcessorios ?: '[]'; ?>;
        let marcaAtual = 'iphone';

        // Renderiza produtos de uma categoria simples (acessórios)
        function renderProdutosSimples(lista, areaId) {
            const area = document.getElementById(areaId);
            if (!area) return;
            area.innerHTML = '';
            if (lista.length) {
                lista.forEach(prod => {
                    let acoes = '';
                    if (isAdmin) {
                        acoes = `<div class="acoes-admin">
                            <a href="editar_produto.php?id=${prod.id}" class="btn-admin">Editar</a>
                            <a href="excluir_produto.php?id=${prod.id}" class="btn-admin excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                        </div>`;
                    }
                    area.innerHTML += `<div class="produto">
                        <img src="${prod.img}" alt="${prod.nome}">
                        <h3>${prod.nome}</h3>
                        <div class="preco">${prod.preco}</div>
                        ${acoes}
                    </div>`;
                });
            } else {
                area.innerHTML = '<p>Nenhum produto disponível nesta categoria.</p>';
            }
        }

        // --- Pulseiras: filtrar por modelo ---
        function renderPulseirasPorModelo(modeloSelecionado) {
            const area = document.getElementById('produtos-pulseiras');
            if (!area) return;
            area.innerHTML = '';

            const lista = pulseirasPorModelo[modeloSelecionado] || [];
            if (lista.length) {
                lista.forEach(prod => {
                    let acoes = '';
                    if (isAdmin) {
                        acoes = `<div class="acoes-admin">
                            <a href="editar_produto.php?id=${prod.id}" class="btn-admin">Editar</a>
                            <a href="excluir_produto.php?id=${prod.id}" class="btn-admin excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                        </div>`;
                    }
                    area.innerHTML += `<div class="produto">
                        <img src="${prod.img}" alt="${prod.nome}">
                        <h3>${prod.nome}</h3>
                        <div class="preco">${prod.preco}</div>
                        ${acoes}
                    </div>`;
                });
            } else {
                area.innerHTML = '<p>Nenhuma pulseira disponível para este modelo.</p>';
            }
        }

        function renderModelosPulseiras() {
            const lista = document.getElementById('lista-modelos-pulseiras');
            if (!lista) return;
            lista.innerHTML = '';

            if (!Array.isArray(modelosPulseiras) || modelosPulseiras.length === 0) {
                const area = document.getElementById('produtos-pulseiras');
                if (area) {
                    area.innerHTML = '<p>Nenhuma pulseira cadastrada.</p>';
                }
                return;
            }

            modelosPulseiras.forEach((modelo, index) => {
                const li = document.createElement('li');
                li.textContent = modelo;
                li.setAttribute('data-modelo', modelo);
                if (index === 0) {
                    li.classList.add('selected');
                }
                li.addEventListener('click', function() {
                    document.querySelectorAll('#lista-modelos-pulseiras li').forEach(el => el.classList.remove('selected'));
                    this.classList.add('selected');
                    renderPulseirasPorModelo(this.getAttribute('data-modelo'));
                });
                lista.appendChild(li);
            });

            // Renderiza pulseiras do primeiro modelo por padrão
            renderPulseirasPorModelo(modelosPulseiras[0]);
        }
        function renderModelos(marca) {
            marcaAtual = marca;
            const lista = document.getElementById('lista-modelos');
            lista.innerHTML = '';
            const modelos = modelosPorMarca[marca] || [];
            modelos.forEach(modelo => {
                const li = document.createElement('li');
                li.textContent = modelo;
                li.setAttribute('data-modelo', modelo);
                li.addEventListener('click', function() {
                    document.querySelectorAll('#lista-modelos li').forEach(el => el.classList.remove('selected'));
                    this.classList.add('selected');
                    renderProdutos(marcaAtual, this.getAttribute('data-modelo'));
                });
                lista.appendChild(li);
            });
        }
        function renderProdutos(marca, modelo = null) {
            const area = document.getElementById('produtos-celular');
            area.innerHTML = '';
            const lista = produtosPorMarca[marca] || [];
            const filtrados = modelo ? lista.filter(prod => prod.modelo === modelo) : lista;
            if (filtrados.length) {
                filtrados.forEach(prod => {
                    let acoes = '';
                    if (isAdmin) {
                        acoes = `<div class="acoes-admin">
                            <a href="editar_produto.php?id=${prod.id}" class="btn-admin">Editar</a>
                            <a href="excluir_produto.php?id=${prod.id}" class="btn-admin excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                        </div>`;
                    }
                    area.innerHTML += `<div class="produto">
                        <img src="${prod.img}" alt="${prod.nome}">
                        <h3>${prod.nome}</h3>
                        <div class="preco">${prod.preco}</div>
                        ${acoes}
                    </div>`;
                });
            } else {
                area.innerHTML = '<p>Nenhum produto disponível para este modelo.</p>';
            }
        }
        // Alterna entre as categorias (Capinhas, Pulseiras, Acessórios)
        document.querySelectorAll('#lista-categorias li').forEach(li => {
            li.addEventListener('click', function() {
                document.querySelectorAll('#lista-categorias li').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');

                const categoria = this.getAttribute('data-categoria');
                document.querySelectorAll('.box-principal').forEach(box => box.classList.remove('ativo'));
                const boxAtiva = document.getElementById('box-' + categoria);
                if (boxAtiva) {
                    boxAtiva.classList.add('ativo');
                }

                if (categoria === 'pulseiras') {
                    renderModelosPulseiras();
                }
            });
        });

        // Troca de marca/modelo dentro da categoria Capinhas
        document.querySelectorAll('#lista-celulares li').forEach(li => {
            li.addEventListener('click', function() {
                document.querySelectorAll('#lista-celulares li').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                const marca = this.getAttribute('data-modelo');
                renderProdutos(marca);
                renderModelos(marca);
            });
        });
        renderProdutos('iphone');
        renderModelos('iphone');
        renderModelosPulseiras();
        renderProdutosSimples(produtosAcessorios, 'produtos-acessorios');
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

        // Botão de menu (mesmo padrão da página inicial)
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
