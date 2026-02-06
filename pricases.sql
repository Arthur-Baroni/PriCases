-- Banco de dados para o site PriCases
-- Script compatível com MySQL/MariaDB

-- Cria o banco de dados (ajuste o nome se quiser)
CREATE DATABASE IF NOT EXISTS pricases
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE pricases;

-- Tabela de clientes/usuários
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  telefone VARCHAR(20),
  senha_hash VARCHAR(255) NOT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de administradores
CREATE TABLE IF NOT EXISTS administradores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de produtos (capinhas, pulseiras, acessórios)
CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(200) NOT NULL,
  categoria VARCHAR(30) NOT NULL DEFAULT 'capinhas', -- Ex: 'capinhas', 'pulseiras', 'acessorios'
  marca VARCHAR(50) NOT NULL,         -- Ex: 'iphone', 'samsung', 'xiaomi', 'motorola', 'outros'
  modelo VARCHAR(100) NOT NULL,       -- Ex: 'iPhone 15 Pro Max'
  preco DECIMAL(10,2) NOT NULL,       -- Ex: 39.90
  imagem_arquivo VARCHAR(255) NOT NULL, -- Nome do arquivo na pasta imagens (ex: 'iphone15-floral.png')
  descricao TEXT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Se o banco já existir sem a coluna de categoria, execute também:
-- ALTER TABLE produtos ADD COLUMN categoria VARCHAR(30) NOT NULL DEFAULT 'capinhas' AFTER nome;

-- Exemplo de administrador inicial (DEPOIS troque a senha por um hash real)
-- A senha aqui é apenas ilustrativa, NÃO use em produção.
INSERT INTO administradores (usuario, email, senha_hash)
VALUES ('admin', 'admin@pricases.com', 'senha-em-texto-plano-trocar-por-hash');

-- Observações:
-- - Campo senha_hash deve guardar a senha já criptografada (por exemplo usando bcrypt no backend).
-- - Para conectar o site, será preciso criar um backend (PHP, Node, etc.)
--   que leia/grave nessas tabelas a partir dos formulários HTML.
