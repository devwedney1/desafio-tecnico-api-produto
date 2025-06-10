DROP DATABASE IF EXISTS produto;

CREATE DATABASE IF NOT EXISTS produto;

USE produto;

DROP TABLE IF EXISTS produtos;

CREATE TABLE IF NOT EXISTS produtos
(
    id            CHAR(36) PRIMARY KEY, -- UUID
    nome          VARCHAR(255) NOT NULL,
    tipo          VARCHAR(100),
    valorProduto  DECIMAL(10, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS compras
(
    id           CHAR(36) PRIMARY KEY, -- UUID
    idProduto    CHAR(36)       NOT NULL,
    valorEntrada DECIMAL(10, 2) NOT NULL,
    CONSTRAINT chk_qtdParcelas CHECK (qtdParcelas > 1),
    FOREIGN KEY (idProduto) REFERENCES produtos (id)
);

CREATE TABLE IF NOT EXISTS parcelas
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    idCompra      CHAR(36),
    numeroParcela INT,
    valorParcela  DECIMAL(10, 2),
    jurosAplicadoId DECIMAL(5, 4),
    FOREIGN KEY (idCompra) REFERENCES compras (id),
    FOREING KEY (juroAplicadoId) REFERENCES taxa_juros (id)
);

CREATE TABLE IF NOT EXISTS taxa_juros
(
    id         INT PRIMARY KEY,
    taxa       DECIMAL(5, 4) NOT NULL,
    dataInicio DATE          NOT NULL,
    dataFinal  DATE          NOT NULL
);