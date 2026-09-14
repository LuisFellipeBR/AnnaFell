-- =============================================
-- Banco de dados: annafell
-- =============================================

CREATE DATABASE IF NOT EXISTS annafell
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE annafell;

-- =============================================
-- Tabela: usuarios
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(100),
    bairro VARCHAR(50),
    cidade VARCHAR(50),
    estado CHAR(2),
    cep VARCHAR(10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabela: logins
-- =============================================
CREATE TABLE IF NOT EXISTS logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(32) NOT NULL,   -- MD5 gera 32 caracteres
    cpf VARCHAR(14) NOT NULL,
    CONSTRAINT fk_login_usuario FOREIGN KEY (cpf) REFERENCES usuarios(cpf) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabela: vendas (cabeçalho)
-- =============================================
CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) UNIQUE NOT NULL,
    cpf_cliente VARCHAR(14) NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    pagamento VARCHAR(30) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_venda_usuario FOREIGN KEY (cpf_cliente) REFERENCES usuarios(cpf) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Tabela: vendas_itens (itens da venda)
-- =============================================
CREATE TABLE IF NOT EXISTS vendas_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venda INT NOT NULL,
    produto VARCHAR(100) NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_venda FOREIGN KEY (id_venda) REFERENCES vendas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;