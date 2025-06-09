DROP DATABASE IF EXISTS produto;

CREATE DATABASE IF NOT EXISTS produto;

USE produto;

CREATE TABLE IF NOT EXISTS produtos
(
    id    CHAR(36) PRIMARY KEY, -- UUID
    nome  VARCHAR(255)   NOT NULL,
    tipo  VARCHAR(100),
    valor DECIMAL(10, 2) NOT NULL CHECK (valor >= 0)
);

CREATE TABLE IF NOT EXISTS compras
(
    id           CHAR(36) PRIMARY KEY, -- UUID
    valorEntrada DECIMAL(10, 2) NOT NULL,
    qtdParcelas  INT            NOT NULL CHECK (qtdParcelas >= 0),
    idProduto    CHAR(36)       NOT NULL,
    FOREIGN KEY (idProduto) REFERENCES produtos (id)
);

CREATE TABLE IF NOT EXISTS parcelas
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    idCompra      CHAR(36),
    numeroParcela INT,
    valorParcela  DECIMAL(10, 2),
    jurosAplicado DECIMAL(5, 4),
    FOREIGN KEY (idCompra) REFERENCES compras (id)
);

CREATE TABLE IF NOT EXISTS taxa_juros
(
    id         INT PRIMARY KEY,
    taxa       DECIMAL(5, 4) NOT NULL,
    dataInicio DATE          NOT NULL,
    dataFinal  DATE          NOT NULL
);