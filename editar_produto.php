<?php
session_start();

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: inicial.php');
    exit;
}

$host    = 'localhost';
$usuario = 'root';
$senhaDb = '0000';
$banco   = 'pricases';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: catalogo.php');
    exit;
}

$mysqli = new mysqli($host, $usuario, $senhaDb, $banco);
if ($mysqli->connect_errno) {
    die('Erro ao conectar ao banco de dados.');
}

$stmt = $mysqli->prepare('SELECT nome, categoria, marca, modelo, preco, imagem_arquivo, descricao FROM produtos WHERE id = ? AND ativo = 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
$produto = $resultado->fetch_assoc();

if (!$produto) {
    $stmt->close();
    $mysqli->close();
    header('Location: catalogo.php');
    exit;
}

$stmt->close();
$mysqli->close();

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto | PriCases</title>
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
        .form-box {
            background: #EEE2DB;
            border-radius: 12px;
            box-shadow: 0 2px 8px #0002;
            padding: 32px 24px;
            max-width: 500px;
            width: 100%;
        }
        .form-box h2 {
            margin-top: 0;
            color: #C28161;
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 4px 0;
            font-weight: 500;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        textarea { resize: vertical; min-height: 80px; }
        .botoes {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .botoes button, .botoes a {
            flex: 1;
            text-align: center;
            border-radius: 999px;
            padding: 10px 16px;
            border: none;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-salvar {
            background: #C28161;
            color: #fff;
        }
        .btn-voltar {
            background: #fff;
            color: #C28161;
            border: 1px solid #C28161;
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
        </nav>
    </header>
    <main>
        <div class="form-box">
            <h2>Editar Produto</h2>
            <form method="post" action="salvar_produto.php">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required value="<?php echo h($produto['nome']); ?>">

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="capinhas" <?php echo ($produto['categoria'] === 'capinhas' ? 'selected' : ''); ?>>Capinhas</option>
                    <option value="pulseiras" <?php echo ($produto['categoria'] === 'pulseiras' ? 'selected' : ''); ?>>Pulseiras</option>
                    <option value="acessorios" <?php echo ($produto['categoria'] === 'acessorios' ? 'selected' : ''); ?>>Acessórios</option>
                </select>

                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" required value="<?php echo h($produto['marca']); ?>">

                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" required value="<?php echo h($produto['modelo']); ?>">

                <label for="preco">Preço (ex: 39,90)</label>
                <input type="text" id="preco" name="preco" required value="<?php echo number_format((float)$produto['preco'], 2, ',', '.'); ?>">

                <label for="imagem">Arquivo da imagem (em imagens/)</label>
                <input type="text" id="imagem" name="imagem" required value="<?php echo h($produto['imagem_arquivo']); ?>">

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao"><?php echo h($produto['descricao']); ?></textarea>

                <div class="botoes">
                    <button type="submit" class="btn-salvar">Salvar</button>
                    <a href="catalogo.php" class="btn-voltar">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
    <footer>
        <div>PriCases &copy; 2026 | WhatsApp: (99) 99999-9999 | Instagram: @pricases</div>
    </footer>
</body>
</html>
