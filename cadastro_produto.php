<?php
session_start();

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

if (!$isAdmin) {
    header('Location: inicial.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto | PriCases</title>
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
        .cadastro-produto-box {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 32px 24px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .cadastro-produto-box h2 {
            margin-top: 0;
            color: #C28161;
        }
        .cadastro-produto-form label {
            display: block;
            margin: 10px 0 4px 0;
            font-weight: 500;
            text-align: left;
        }
        .cadastro-produto-form input,
        .cadastro-produto-form select,
        .cadastro-produto-form textarea {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .cadastro-produto-form textarea {
            resize: vertical;
            min-height: 60px;
        }
        .cadastro-produto-form button {
            background: #C28161;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .cadastro-produto-form button:hover {
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
            .cadastro-produto-box { padding: 18px 5px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a href="inicial.php"><img src="imagens/Logo.png" alt="PriCases"></a></div>
        <nav>
            <a href="inicial.php">Home</a>
            <a href="catalogo.php">Catálogo</a>
        </nav>
    </header>
    <main>
        <div class="cadastro-produto-box">
            <h2>Cadastro de Produto</h2>
            <form class="cadastro-produto-form" method="post" action="cadastrar_produto.php">
                <label for="nome-produto">Nome do produto</label>
                <input type="text" id="nome-produto" name="nome-produto" required>

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="capinhas" selected>Capinhas</option>
                    <option value="pulseiras">Pulseiras</option>
                    <option value="acessorios">Acessórios</option>
                </select>

                <label for="marca">Marca do celular (obrigatório apenas para Capinhas)</label>
                <select id="marca" name="marca">
                    <option value="">Selecione</option>
                    <option value="iphone">iPhone</option>
                    <option value="samsung">Samsung</option>
                    <option value="xiaomi">Xiaomi</option>
                    <option value="motorola">Motorola</option>
                    <option value="outros">Outros</option>
                </select>

                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" placeholder="Ex: iPhone 15 Pro Max" required>

                <label for="preco">Preço</label>
                <input type="text" id="preco" name="preco" placeholder="Ex: R$ 39,90" required>

                <label for="imagem">Arquivo da imagem (nome do arquivo na pasta imagens)</label>
                <input type="text" id="imagem" name="imagem" placeholder="Ex: iphone15-floral.png" required>

                <label for="descricao">Descrição (opcional)</label>
                <textarea id="descricao" name="descricao" placeholder="Breve descrição do produto"></textarea>

                <button type="submit">Cadastrar produto</button>
            </form>
        </div>
    </main>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
    <script>
        // Marca só é obrigatória quando a categoria for "capinhas"
        (function() {
            var categoriaSelect = document.getElementById('categoria');
            var marcaSelect = document.getElementById('marca');

            if (!categoriaSelect || !marcaSelect) return;

            function atualizarObrigatoriedade() {
                var ehCapinhas = (categoriaSelect.value === 'capinhas');
                marcaSelect.required = ehCapinhas;
            }

            categoriaSelect.addEventListener('change', atualizarObrigatoriedade);
            atualizarObrigatoriedade();
        })();

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
    </script>
</body>
</html>
