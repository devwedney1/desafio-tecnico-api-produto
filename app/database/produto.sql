-- Apaga e recria o banco
DROP DATABASE IF EXISTS produto;
CREATE DATABASE produto;
USE produto;

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos
(
    id           CHAR(36) PRIMARY KEY,
    nome         VARCHAR(255)   NOT NULL,
    tipo         VARCHAR(100),
    valorProduto DECIMAL(10, 2) NOT NULL,

    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at   DATETIME NULL
);

-- Tabela de taxas de juros
CREATE TABLE IF NOT EXISTS taxa_juros
(
    id          CHAR(36) PRIMARY KEY,
    taxa       DECIMAL(5, 4) NOT NULL, -- ex: 0.1375 = 13.75%
    dataInicio DATE          NOT NULL,
    dataFinal  DATE          NOT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,

    CONSTRAINT chk_datas CHECK (dataInicio < dataFinal)
);

-- Tabela de compras
CREATE TABLE IF NOT EXISTS compras
(
    id           CHAR(36) PRIMARY KEY,
    idProduto    CHAR(36)       NOT NULL,
    valorEntrada DECIMAL(15, 2) NOT NULL,
    qtdParcelas  INT            NOT NULL,
    dataCompra   DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idTaxaJuros  CHAR(36)            NOT NULL,

    created_at   DATETIME                DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME                DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at   DATETIME NULL,

    CONSTRAINT chk_parcelas CHECK (qtdParcelas > 0),
    CONSTRAINT chk_entrada CHECK (valorEntrada >= 0),
    FOREIGN KEY (idProduto) REFERENCES produtos (id),
    FOREIGN KEY (idTaxaJuros) REFERENCES taxa_juros (id)
);

-- Tabela de parcelas
CREATE TABLE IF NOT EXISTS parcelas
(
    id             CHAR(36) PRIMARY KEY,
    idCompra       CHAR(36)       NOT NULL,
    numeroParcela  INT            NOT NULL,
    valorParcela   DECIMAL(10, 2) NOT NULL,
    dataVencimento DATE           NOT NULL,

    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at     DATETIME NULL,

    CONSTRAINT chk_valor_parcela CHECK (valorParcela > 0),
    FOREIGN KEY (idCompra) REFERENCES compras (id)
);
